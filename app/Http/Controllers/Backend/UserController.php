<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\UserResource;
use App\Mail\SendPasswordCreateAdminEmail;
use App\Models\Filters\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
            $data = $request->all();
            $user = User::find($id);
            if (!$user) {
                return $this->sendErrorResponse(__('Data not found'), 404);
            }
            DB::beginTransaction();
            $user->update($data);
            $user->syncRoles($data['role_id']);
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
            $user->delete();
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
