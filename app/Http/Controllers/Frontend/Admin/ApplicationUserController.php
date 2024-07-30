<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
use App\Models\System;
use Inertia\Inertia;

class ApplicationUserController extends Controller
{
    public function index()
    {
        $user = $this->getCurrentUser();
        $userPermissions =  $user->getAllPermissions();
        $groupedPermissions = $userPermissions->map(function ($permission) {
            $permissionCode = explode('-', $permission->name);
            $systemCode = $permissionCode[0];
            $system = System::where('code', $systemCode)->first();
            $permission->system = $system;
            return $permission;
        })->groupBy(function ($permission) {
            return $permission->system->code;
        });

        $applications = $groupedPermissions->map(function ($permissions) {
            $system = $permissions[0]->system;
            return [
                'id' => $system->id,
                'name' => $system->name,
                'code' => $system->code,
                'permissions' => $permissions->pluck('name')->toArray(),
            ];
        })->values();
        return Inertia::render('ApplicationUser/Index', [
            'userApplications' => $applications,
        ]);
    }
}
