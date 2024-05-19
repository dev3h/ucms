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
            ['name' => 'admin', 'code' => 'ADMIN'],
            ['name' => 'teacher', 'code' => 'TEACHER'],
            ['name' => 'student', 'code' => 'STUDENT'],
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
        $adminRole = Role::where('name', 'admin')->first();
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        // Assign specific permissions to the lecturer role
        $facultyRole = Role::where('name', 'teacher')->first();
        $facultyRole->syncPermissions($permissions->random(5));

        // Assign specific permissions to the student role
        $studentRole = Role::where('name', 'student')->first();
        $studentRole->syncPermissions($permissions->random(1));

    }
}
