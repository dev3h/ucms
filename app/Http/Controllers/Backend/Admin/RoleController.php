<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RestPermissionResource;
use App\Http\Resources\RolePermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleUserResource;
use App\Models\Action;
use App\Models\Filters\PermissionFilter;
use App\Models\Filters\RoleFilter;
use App\Models\Filters\UserFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Role::filters(new RoleFilter($request))
                ->orderBy('created_at', 'desc')
                ->paginate(PerPage::DEFAULT);
            return RoleResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            return $this->sendSuccessResponse(RoleResource::make($role));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function getAllUserOfRole($id, Request $request)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $users = $role->users()->filters(new UserFilter($request))->orderBy('created_at', 'desc')->paginate(PerPage::DEFAULT);
            return RoleUserResource::collection($users)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function getAllPermissionOfRole($id, Request $request)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $permissions = $role->permissions()->filters(new PermissionFilter($request))
                ->orderBy('created_at', 'desc')->paginate(PerPage::DEFAULT);

            return RolePermissionResource::collection($permissions)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function store(RoleRequest $request)
    {
        try {
            $data = $request->all();
            DB::beginTransaction();
            $role = Role::create([
                'name' => $data['name'],
                'code' => $data['code'],
            ]);
            $actionIds = $data['actions'];
            foreach ($actionIds as $actionId) {
                $action = Action::find($actionId);
                $system = $action->module->subsystem->system;
                $subsystem = $action->module->subsystem;
                $module = $action->module;
                $permissionCode = $system->code . '-' . $subsystem->code . '-' . $module->code . '-' . $action->code;
                $permission = Permission::where('code', $permissionCode)->first();
                if ($permission) {
                    $role->givePermissionTo($permission);
                }
            }
            DB::commit();
            return $this->sendSuccessResponse(null,__('Create successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function update($id, RoleRequest $request)
    {
        try {
            $data = $request->all();
            DB::beginTransaction();
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $role->update([
                'name' => $data['name'],
                'code' => $data['code'],
            ]);
            $actionSelects = $data['actions'];
            if(count($actionSelects) === 0) {
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
                        $role->givePermissionTo($permission);
                    } else {
                        $role->revokePermissionTo($permission);
                    }
                }
            }
            DB::commit();
            return $this->sendSuccessResponse(null,__('Update successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $role->delete();
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function assignUser(Request $request, $id)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $userIds = $request->user_ids;
            $role->users()->sync($userIds);
            return $this->sendSuccessResponse(null, __('Assign user successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function revokeUser(Request $request, $id)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $currentUser = $this->getCurrentUser();
            if($currentUser->id === +$request->user_id) {
                return $this->sendErrorResponse(__('You can not revoke yourself'), 400);
            }
            $role->users()->detach($request->user_id);
            return $this->sendSuccessResponse(null, __('Revoke user successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function revokePermission(Request $request, $id)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $permission = Permission::find($request->permission_id);
            $role->revokePermissionTo($permission);
            return $this->sendSuccessResponse(null, __('Revoke permission successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function restPermission($id)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $permissions = Permission::all();
            $permissionOfRole = $role->permissions;
            $diffPermissions = $permissions->diff($permissionOfRole);
            $data = new RestPermissionResource($diffPermissions);
            return $this->sendSuccessResponse($data);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function getAllPermission()
    {
        try {
            $permissions = Permission::all();
            $data = new RestPermissionResource($permissions);
            return $this->sendSuccessResponse($data);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function assignPermission(Request $request, $id)
    {
        try {
            $role = Role::find($id);
            if(!$role) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            $permissionIds = $request->permission_ids;
            $role->permissions()->attach($permissionIds);
            return $this->sendSuccessResponse(null, __('Assign permission successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

}
