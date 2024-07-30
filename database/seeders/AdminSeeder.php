<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "nam nd",
                "email" => "namnd@yopmail.com",
                'password' => bcrypt('a12345678X'),
                'is_change_password_first' => 1
            ],
            [
                "name" => "cuongdd",
                "email" => "cuongdd@yopmail.com",
                'password' => bcrypt('a12345678X'),
                'is_change_password_first' => 1
            ]
        ];
        foreach ($data as $item) {
            $user = Admin::create($item);
            $user->assignRole("master_admin");
            $user->syncPermissions($user->roles->first()->permissions);
        }
    }
}
