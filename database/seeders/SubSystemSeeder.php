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
        $subSystems = [
            ['name' => 'Quản lý sinh viên', 'code' => 'STU', 'system_id' => 1],
            ['name' => 'Quản lý giảng viên', 'code' => 'TEA', 'system_id' => 1],
            ['name' => 'Quản lý học tập', 'code' => 'ACA', 'system_id' => 1],
            ['name' => 'Quản lý đào tạo', 'code' => 'TRN', 'system_id' => 1],
            ['name' => 'Quản lý nghiên cứu khoa học', 'code' => 'RES', 'system_id' => 2],
            ['name' => 'Quản lý hành chính', 'code' => 'ADM', 'system_id' => 3],
            ['name' => 'Quản lý tài chính', 'code' => 'FIN', 'system_id' => 3],
            ['name' => 'Quản lý cơ sở vật chất', 'code' => 'INF', 'system_id' => 3],
        ];

        foreach ($subSystems as $subSystem) {
            \App\Models\SubSystem::create($subSystem);
        }
    }
}
