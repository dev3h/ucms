<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $system = \App\Models\System::all();
        for($i = 1; $i <= 10; $i++) {
            \App\Models\SubSystem::create([
                'name' => 'Hệ thống con ' . $i,
                'code' => 'SUBSYS' . $i,
                'system_id' => $system->random()->id,
            ]);
        }


    }
}
