<?php

namespace App\Http\Controllers\Backend;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\TemplatePermissionResource;
use App\Models\Filters\PermissionFilter;
use App\Models\System;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Permission::filters(new PermissionFilter($request))->paginate(PerPage::DEFAULT);
            return PermissionResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
    public function templatePermission($id)
    {
        try {
            if(request()->routeIs('*api.role*')) {
                $role = Role::find($id);
                $permissionsOfRole = $role->getAllPermissions();
                $template = null;
                $systems = System::all();
                foreach($systems as $system) {
                    $subsystems = $system->subsystems;
                    foreach($subsystems as $subsystem) {
                        $modules = $subsystem->modules;
                        foreach($modules as $module) {
                            $actions = $module->actions;
                            foreach($actions as $action) {
                                $permissionCode = $system?->code . '-' . $subsystem?->code . '-' . $module?->code . '-' . $action?->code;
                                $permission = Permission::where('code', $permissionCode)->first();
                                if($permission) {
                                    if($permissionsOfRole->contains($permission)) {
                                        $action->checked = true;
                                    } else {
                                        $action->checked = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return TemplatePermissionResource::collection($systems)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
    public function updateTemplatePermission($id, Request $request)
    {
        try {
           if(request()->routeIs('*api.role*')) {
                $role = Role::find($id);
                $systems = System::all();
                foreach($systems as $system) {
                    $subsystems = $system->subsystems;
                    foreach($subsystems as $subsystem) {
                        $modules = $subsystem->modules;
                        foreach($modules as $module) {
                            $actions = $module->actions;
                            foreach($actions as $action) {
                                $permissionCode = $system?->code . '-' . $subsystem?->code . '-' . $module?->code . '-' . $action?->code;
                                $permission = Permission::where('code', $permissionCode)->first();
                                if($permission) {
                                    if($request->actions) {
                                       foreach($request->actions as $item) {
                                           if($item['id'] === $action->id) {
                                               if($item['checked']) {
                                                   $role->givePermissionTo($permission);
                                               } else {
                                                   $role->revokePermissionTo($permission);
                                               }
                                           }
                                       }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $this->sendSuccessResponse(null, __('Updated successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
