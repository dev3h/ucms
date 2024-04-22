<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Resources\TemplatePermissionResource;
use App\Models\System;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return Inertia::render('User/Index', [
            'roles' => $roles,
        ]);
    }
    public function show($id)
    {
        $roles = Role::all();
        return Inertia::render('User/Show', [
            'id' => +$id,
            'roles' => $roles,
        ]);
    }
    public function create(Request $request)
    {
        $roles = Role::all();
        if($request->input('role_id')) {
            $role = Role::find($request->input('role_id'));
            $permissionOfRole = $role->permissions;
            dd($permissionOfRole);
        }
//        dd($permissionOfRole);
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
        return Inertia::render('User/Create', [
            'roles' => $roles,
        ]);
    }
}
