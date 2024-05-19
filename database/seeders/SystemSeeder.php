<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 1; $i <= 10; $i++) {
            \App\Models\System::create([
                'name' => 'Hệ thống ' . $i,
                'code' => 'SYS' . $i,
            ]);
        }
    }
}
