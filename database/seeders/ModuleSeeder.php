<?php

namespace Database\Seeders;

use App\Models\SubSystem;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subsystems = SubSystem::all();
        for($i = 1; $i <= 10; $i++) {
            \App\Models\Module::create([
                'name' => 'Module ' . $i,
                'code' => 'MOD' . $i,
            ]);
        }
        $modules = \App\Models\Module::all();
        foreach ($subsystems as $subsystem) {
            $subsystem->modules()->attach($modules->random(rand(1, 5)));
        }
    }
}
