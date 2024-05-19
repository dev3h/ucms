<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = \App\Models\Module::all();
        for($i = 1; $i <= 10; $i++) {
            \App\Models\Action::create([
                'name' => 'Action ' . $i,
                'code' => 'ACT' . $i,
            ]);
        }
        $actions = \App\Models\Action::all();
        foreach ($modules as $module) {
            $module->actions()->attach($actions->random(rand(1, 5)));
        }
    }
}
