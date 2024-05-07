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
        $systems = [
            ['name' => 'Quản lý Học vụ', 'code' => 'ACM'],
            ['name' => 'Quản lý Nghiên cứu Khoa học', 'code' => 'RES'],
            ['name' => 'Quản lý Hành chính', 'code' => 'ADM'],
        ];

        foreach ($systems as $system) {
            \App\Models\System::create($system);
        }
    }
}
