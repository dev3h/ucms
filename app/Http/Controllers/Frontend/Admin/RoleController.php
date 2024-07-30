<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TemplatePermissionResource;
use App\Models\System;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Role/Index');
    }
    public function create()
    {
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
                            $action->checked = false;
                        }
                    }
                }
            }
        }
        $templatePermission = TemplatePermissionResource::collection($systems);
        return Inertia::render('Role/Create', [
            'templatePermission' => $templatePermission->resolve(),
        ]);
    }
    public function show($id)
    {
        return Inertia::render('Role/Show', ['id' => +$id]);
    }
}
