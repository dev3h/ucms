<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Consts\PerPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\TemplatePermissionResource;
use App\Http\Resources\TemplateUserPermissionResource;
use App\Models\Filters\PermissionFilter;
use App\Models\System;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Permission::filters(new PermissionFilter($request))->orderBy('created_at', 'desc')->paginate(PerPage::DEFAULT);
            return PermissionResource::collection($data)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function store(PermissionRequest $request)
    {
        try {
            $data = $request->all();
            $permission = Permission::create($data);
            return $this->sendSuccessResponse($permission, __('Created successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $permission = Permission::find($id);
            return $this->sendSuccessResponse($permission, __('Get data successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function update(PermissionRequest $request, $id)
    {
        try {
            $data = $request->all();
            $permission = Permission::find($id);
            $permission->update($data);
            return $this->sendSuccessResponse($permission, __('Updated successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $permission = Permission::find($id);
            $permission->delete();
            return $this->sendSuccessResponse(null, __('Deleted successfully'));
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }

    public function getCodeForPermission()
    {
        try {
            $systems = System::all();

            $data = $systems->map(function ($system) {
                return [
                    'id' => $system->id,
                    'name' => $system->name,
                    'code' => $system->code,
                    'subsystems' => $system->subsystems->map(function ($subsystem) {
                        return [
                            'id' => $subsystem->id,
                            'name' => $subsystem->name,
                            'code' => $subsystem->code,
                            'modules' => $subsystem->modules->map(function ($module) {
                                return [
                                    'id' => $module->id,
                                    'name' => $module->name,
                                    'code' => $module->code,
                                    'actions' => $module->actions->map(function ($action) {
                                        return [
                                            'id' => $action->id,
                                            'name' => $action->name,
                                            'code' => $action->code,
                                        ];
                                    }),
                                ];
                            }),
                        ];
                    }),
                ];
            });
            return $this->sendSuccessResponse($data);
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
    public function templatePermissionUser($id, Request $request)
    {
        try {
            $user = User::find($id);
            $role = $user->roles->first();
            if($request->input('role_id')) {
                $role = Role::find($request->input('role_id'));
            }
            $permissionsOfUser = $user->getAllPermissions();
            $permissionsOfRole = $role->getAllPermissions();
            $userRole = $user->roles->first();
            $permissionsOfUserRole = $userRole->getAllPermissions();
            $extraPermissions = $permissionsOfUser->diff($permissionsOfUserRole);
            $permissionReal = $extraPermissions->merge($permissionsOfRole);
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
                                    $action->disabled = true;
                                } else {
                                    $action->disabled = false;
                                }
                                if($permissionReal->contains($permission)) {
                                    $action->checked = true;
                                } else {
                                    $action->checked = false;
                                }
                            }
                        }
                    }
                }
            }
            return TemplateUserPermissionResource::collection($systems)
                ->additional(["status_code" => 200]);
        } catch (\Throwable $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
