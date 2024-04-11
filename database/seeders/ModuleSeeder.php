<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['name' => 'Tạo Khóa học', 'code' => 'CRC', 'sub_system_id' => 1],
            ['name' => 'Chỉnh sửa Khóa học', 'code' => 'EDC', 'sub_system_id' => 1],
            ['name' => 'Ghi nhận Điểm số', 'code' => 'RDG', 'sub_system_id' => 2],
            ['name' => 'Xem Điểm số', 'code' => 'VWG', 'sub_system_id' => 2],
            ['name' => 'Đăng ký Học phần', 'code' => 'CEN', 'sub_system_id' => 3],
            ['name' => 'Xem Thời khóa biểu', 'code' => 'VSC', 'sub_system_id' => 3],
            ['name' => 'Thêm Sách', 'code' => 'ADB', 'sub_system_id' => 4],
            ['name' => 'Chỉnh sửa Sách', 'code' => 'EBK', 'sub_system_id' => 4],
            ['name' => 'Quản lý Ngân sách', 'code' => 'MAN', 'sub_system_id' => 5],
            ['name' => 'Thêm Nhân viên', 'code' => 'EMP', 'sub_system_id' => 6],
            ['name' => 'Chỉnh sửa Thông tin Nhân viên', 'code' => 'INF', 'sub_system_id' => 6],
            ['name' => 'Tạo Khóa học đào tạo', 'code' => 'TRC', 'sub_system_id' => 7],
            ['name' => 'Xem Kế hoạch đào tạo', 'code' => 'TPL', 'sub_system_id' => 7],
        ];

        foreach ($modules as $module) {
            \App\Models\Module::create($module);
        }
    }
}
