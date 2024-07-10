<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserLogResource;
use App\Http\Resources\UserResource;
use App\Mail\SendPasswordCreateAdminEmail;
use App\Models\Action;
use App\Models\Filters\PermissionFilter;
use App\Models\Filters\UserLogFilter;
use App\Models\Filters\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = User::filters(new UserFilter($request))
                ->orderBy('created_at', 'desc')
                ->paginate(PerPage::DEFAULT);
            return UserResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
    public function show($id)
    {
        try {
            $data = User::find($id);
            if (!$data) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            return $this->sendSuccessResponse(UserResource::make($data));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Data not found'), $e->getMessage());
        }
    }
    public function store(UserRequest $request)
    {
        $dataValidate = $request->validated();
        DB::beginTransaction();
        try {
            $realPassword = Str::random(16);
            $accountData = [
                'name' => $dataValidate['name'],
                'email' => $dataValidate['email'],
                'password' => Hash::make($realPassword),
                'real_password' => $realPassword,
            ];
            $account = User::create(Arr::except($accountData, ['real_password']));
            $role = Role::find($dataValidate['role_id']);
            $account->assignRole($role);

            $loginUrl = route('admin.login.form');
            $data_send = [
                'user' => [
                    'name' => $accountData['name'],
                    'email' => $accountData['email'],
                    'created_at' => $account->created_at,
                    'real_password' => $accountData['real_password'],
                    'is_change_password' => $account->pass_is_changed
                ],
                'admin' => [
                    'name' => $this->getCurrentUser()->name,
                ],
                'url' => $loginUrl,
            ];
            Mail::to($dataValidate['email'])->send(new SendPasswordCreateAdminEmail($data_send));

            DB::commit();
            return $this->sendSuccessResponse(null, __('Created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorResponse($e->getMessage(), $e);
        }
    }

    public function update(UserRequest $request, $id)
    {
        try {
            $data = $request->validated();

            $user = User::find($id);
            if (!$user) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            if ($this->getCurrentUser()->id === $user->id) {
                return $this->sendErrorResponse(__('You can not update yourself'), 403);
            }
            DB::beginTransaction();
            $user->update(Arr::except($data, ['role_id']));
            $user->syncRoles($data['role_id']);

            $actionSelects = $request->input('actions');
            if (count($actionSelects) === 0) {
                DB::commit();
                return $this->sendSuccessResponse(null,__('Update successfully'));
            }

            foreach ($actionSelects as $actionSelect) {
                $action = Action::find($actionSelect['id']);
                $system = $action->module->subsystem->system;
                $subsystem = $action->module->subsystem;
                $module = $action->module;
                $permissionCode = $system->code . '-' . $subsystem->code . '-' . $module->code . '-' . $action->code;
                $permission = Permission::where('code', $permissionCode)->first();
                if ($permission) {
                    if($actionSelect['checked']) {
                        $user->givePermissionTo($permission);
                    } else {
                        $user->revokePermissionTo($permission);
                    }
                }
            }
            DB::commit();
            return $this->sendSuccessResponse(UserResource::make($user), __('Updated successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            if($this->getCurrentUser()->id === $user->id) {
                return $this->sendErrorResponse(__('You can not delete yourself'), 403);
            }
            $user->delete();
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function getAllPermissionOfUser($id, Request $request)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $permissions = $user->permissions()->wherePivot('is_direct', '!=' , 3)->where('code', 'LIKE', '%' . $request->input('search') . '%')
                ->orderBy('created_at', 'desc')->paginate(PerPage::DEFAULT);

            return PermissionResource::collection($permissions)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Data not found'), $e->getMessage());
        }
    }

    public function getAllPermissionOfUserByRole($role_id, $user_id)
    {
        try {
            $user = User::find($user_id);
            if (!$user) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $role = Role::find($role_id);
            if (!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $rolePermissions = $role->permissions()->get();
            $userPermissions = $user->permissions()->get();
            // get same permission of user in role
            $permissions = $userPermissions->intersect($rolePermissions);
            return PermissionResource::collection($permissions)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Data not found'), $e->getMessage());
        }
    }

    public function ignorePermissionForUserRole($id, Request $request)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $permissionIds = $request->input('permission_ids');
            $removePermissionIgnoreIds = $request->input('remove_permission_ignore_ids');
            if (count($permissionIds) > 0) {
                $permissions = $user->permissions()->whereIn('id', $permissionIds)->get();
                foreach ($permissions as $permission) {
                    $permission->pivot->is_direct = 3;
                    $permission->pivot->save();
                }
            }
            if (count($removePermissionIgnoreIds) > 0) {
                $permissions = $user->permissions()->whereIn('id', $removePermissionIgnoreIds)->get();
                foreach ($permissions as $permission) {
                    $permission->pivot->is_direct = 0;
                    $permission->pivot->save();
                }
            }
            return $this->sendSuccessResponse(null, __('Updated successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Data not found'), $e->getMessage());
        }
    }

    public function getUserLogs($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $logs = $user->audits()->filters(new UserLogFilter(request()))->orderBy('created_at', 'desc')
                ->paginate(PerPage::DEFAULT);

            return UserLogResource::collection($logs)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Data not found'), $e->getMessage());
        }
    }
}
