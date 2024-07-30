<?php

namespace Database\Seeders;

use App\Models\System;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'master_admin', 'code' => 'SUPER_ADMIN'],
            ['name' => 'admin', 'code' => 'ADMIN'],
            ['name' => 'user', 'code' => 'USER']
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }

        $systems = System::with('subsystems.modules.actions')->get();

        foreach ($systems as $system) {
            foreach ($system->subsystems as $subsystem) {
                foreach ($subsystem->modules as $module) {
                    foreach ($module->actions as $action) {
                        $permissionName = $system->code . '-' . $subsystem->code . '-' . $module->code . '-' . $action->code;
                        Permission::create(
                            [
                                'name' => $permissionName,
                                'code'=> $permissionName,
                                'guard_name' => 'admin'
                            ]
                        );
                    }
                }
            }
        }

        // Assign full permissions to the administrator role
        $superAdminRole = Role::where('name', 'master_admin')->where('guard_name', 'admin')->first();
        $permissions = Permission::where('guard_name', 'web')->get();
        $superAdminRole?->givePermissionTo($permissions);

        // Assign full permissions to the user role
        $userRole = Role::where('name', 'user')->where('guard_name', 'web')->first();
        $permissions = Permission::where('guard_name', 'web')->get();
        $userRole?->givePermissionTo($permissions);
    }
}
