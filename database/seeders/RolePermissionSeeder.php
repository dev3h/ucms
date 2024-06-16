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
            ['name' => 'super admin', 'code' => 'SUPER_ADMIN'],
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
                            ]
                        );
                    }
                }
            }
        }

        // Assign full permissions to the administrator role
        $superAdminRole = Role::where('name', 'super admin')->first();
        $permissions = Permission::all();
        $superAdminRole->syncPermissions($permissions);
    }
}
