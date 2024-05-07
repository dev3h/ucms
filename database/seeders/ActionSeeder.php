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
            ['name' => 'Tạo mới', 'code' => 'create'],
            ['name' => 'Xem', 'code' => 'view'],
            ['name' => 'Sửa', 'code' => 'edit'],
            ['name' => 'Xóa', 'code' => 'delete'],
            ['name' => 'Xuất file', 'code' => 'export'],
            ['name' => 'Nhập file', 'code' => 'import'],
            ['name' => 'Xem danh sách', 'code' => 'list'],
            ['name' => 'Xem chi tiết', 'code' => 'detail'],
            ['name' => 'Xem lịch sử', 'code' => 'history'],
            ['name' => 'Xem báo cáo', 'code' => 'report'],
            ['name' => 'Xem thống kê', 'code' => 'statistic'],
            ['name' => 'Xem thông báo', 'code' => 'notification'],
            ['name' => 'Xem cài đặt', 'code' => 'setting'],
            ['name' => 'Xem hướng dẫn', 'code' => 'guide'],
            ['name' => 'Xem trợ giúp', 'code' => 'help'],
            ['name' => 'Xem chính sách', 'code' => 'policy'],
            ['name' => 'Xem điều khoản', 'code' => 'term'],
        ];

        foreach ($actions as $action) {
            \App\Models\Action::create($action);
        }
    }
}
