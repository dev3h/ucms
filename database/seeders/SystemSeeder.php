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
            ['name' => 'Quản lý Sinh viên', 'code' => 'STA'],
            ['name' => 'Quản lý Thư viện', 'code' => 'LIB'],
            ['name' => 'Quản lý Tài chính', 'code' => 'FIN'],
            ['name' => 'Quản lý Nhân sự', 'code' => 'HRM'],
            ['name' => 'Quản lý Đào tạo', 'code' => 'TRM'],
        ];

        foreach ($systems as $system) {
            \App\Models\System::create($system);
        }
    }
}
