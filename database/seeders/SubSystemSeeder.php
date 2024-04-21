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
        $subsystems = [
            ['name' => 'Quản lý Khóa học', 'code' => 'CMG', 'system_id' => 1],
            ['name' => 'Quản lý Điểm số', 'code' => 'GRD', 'system_id' => 1],
            ['name' => 'Đăng ký Học phần', 'code' => 'ENR', 'system_id' => 1],
            ['name' => 'Quản lý Đăng ký học', 'code' => 'CRS', 'system_id' => 2],
            ['name' => 'Quản lý Khen thưởng', 'code' => 'AWD', 'system_id' => 2],
            ['name' => 'Quản lý Sách', 'code' => 'BOK', 'system_id' => 3],
            ['name' => 'Quản lý Mượn trả', 'code' => 'BOR', 'system_id' => 3],
            ['name' => 'Quản lý Ngân sách', 'code' => 'BUD', 'system_id' => 4],
            ['name' => 'Quản lý Nhân viên', 'code' => 'EMP', 'system_id' => 5],
            ['name' => 'Quản lý Lương thưởng', 'code' => 'PAY', 'system_id' => 5],
            ['name' => 'Quản lý Khóa học đào tạo', 'code' => 'TRN', 'system_id' => 6],
            ['name' => 'Quản lý Kế hoạch đào tạo', 'code' => 'PLN', 'system_id' => 6],
        ];

        foreach ($subsystems as $subSystem) {
            \App\Models\SubSystem::create($subSystem);
        }
    }
}
