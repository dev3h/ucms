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
        $actions = [
            ['name' => 'Thêm Khóa học', 'code' => 'ACC', 'module_id' => 1],
            ['name' => 'Xóa Khóa học', 'code' => 'DEC', 'module_id' => 1],
            ['name' => 'Nhập Điểm số', 'code' => 'EGD', 'module_id' => 3],
            ['name' => 'Đăng ký Học phần', 'code' => 'ENR', 'module_id' => 5],
            ['name' => 'Xem Thời khóa biểu', 'code' => 'VSC', 'module_id' => 6],
            ['name' => 'Thêm Sách vào thư viện', 'code' => 'ADB', 'module_id' => 7],
            ['name' => 'Xóa Sách khỏi thư viện', 'code' => 'RMB', 'module_id' => 8],
            ['name' => 'Tạo Ngân sách mới', 'code' => 'CNB', 'module_id' => 9],
            ['name' => 'Xem Báo cáo Ngân sách', 'code' => 'RPT', 'module_id' => 9],
            ['name' => 'Thay đổi Vị trí công việc', 'code' => 'JOB', 'module_id' => 11],
            ['name' => 'Xóa Nhân viên', 'code' => 'EMP', 'module_id' => 11],
            ['name' => 'Tạo Khóa học đào tạo mới', 'code' => 'CRT', 'module_id' => 13],
            ['name' => 'Xóa Khóa học đào tạo', 'code' => 'DTR', 'module_id' => 13],
        ];

        foreach ($actions as $action) {
            \App\Models\Action::create($action);
        }
    }
}
