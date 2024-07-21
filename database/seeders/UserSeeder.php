<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
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
                "type" => 1,
                'password' => bcrypt('a12345678X'),
                'is_change_password' => 1
            ],
            [
                "name" => "cuongdd",
                "email" => "cuongdd@yopmail.com",
                "type" => 1,
                'password' => bcrypt('a12345678X'),
                'is_change_password' => 1
            ]
        ];
        foreach ($data as $item) {
            $user = User::create($item);
            $user->assignRole("master_admin");
            $user->syncPermissions($user->roles->first()->permissions);
        }
    }
}
