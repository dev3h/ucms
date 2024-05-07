<?php

namespace Database\Seeders;

use App\Models\SubSystem;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Module liên quan đến sinh viên
        $studentModules = [
            ['name' => 'Quản lý sinh viên', 'code' => 'STU'],
            ['name' => 'Xem danh sách sinh viên', 'code' => 'LST'],
            ['name' => 'Thêm mới sinh viên', 'code' => 'ADD'],
            ['name' => 'Chỉnh sửa thông tin sinh viên', 'code' => 'EDI'],
            ['name' => 'Xóa sinh viên', 'code' => 'DEL'],
            ['name' => 'Xem điểm số sinh viên', 'code' => 'VGR'],
            ['name' => 'Quản lý học bổng sinh viên', 'code' => 'SCH'],
            ['name' => 'Quản lý kỷ luật sinh viên', 'code' => 'DIS'],
        ];

        foreach ($studentModules as $module) {
            \App\Models\Module::create($module);
        }

        // Module liên quan đến giảng viên
        $teacherModules = [
            ['name' => 'Quản lý giảng viên', 'code' => 'TEA'],
            ['name' => 'Xem danh sách giảng viên', 'code' => 'LST'],
            ['name' => 'Thêm mới giảng viên', 'code' => 'ADD'],
            ['name' => 'Chỉnh sửa thông tin giảng viên', 'code' => 'EDI'],
            ['name' => 'Xóa giảng viên', 'code' => 'DEL'],
            ['name' => 'Phân công giảng dạy', 'code' => 'ASS'],
            ['name' => 'Quản lý đánh giá giảng viên', 'code' => 'EVA'],
            ['name' => 'Quản lý lương thưởng giảng viên', 'code' => 'SAL'],
        ];

        foreach ($teacherModules as $module) {
            \App\Models\Module::create($module);
        }

        // Module liên quan đến trường đại học
            $universityModules = [
                ['name' => 'Quản lý khoa/viện', 'code' => 'DEP'],
                ['name' => 'Quản lý ngành học', 'code' => 'MAJ'],
                ['name' => 'Quản lý chuyên ngành', 'code' => 'SPE'],
                ['name' => 'Quản lý học phần', 'code' => 'COU'],
                ['name' => 'Quản lý lịch học', 'code' => 'SCH'],
                ['name' => 'Quản lý điểm thi', 'code' => 'EXA'],
                ['name' => 'Quản lý văn phòng', 'code' => 'OFF'],
                ['name' => 'Quản lý tài chính', 'code' => 'FIN'],
                ['name' => 'Quản lý cơ sở vật chất', 'code' => 'INF'],
            ];

        foreach ($universityModules as $module) {
            \App\Models\Module::create($module);
        }

        $subsystems = SubSystem::all();

        foreach ($subsystems as $subsystem) {
            $modules = \App\Models\Module::all();
            $subsystem->modules()->attach($modules);
        }
    }
}
