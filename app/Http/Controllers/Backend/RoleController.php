<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Action;
use App\Models\Filters\RoleFilter;
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
}
