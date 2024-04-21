<?php

namespace Database\Seeders;

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
        $admin = User::create([
            "name" => "nam nd",
            "email" => "namnd@yopmail.com",
            'password' => bcrypt('a12345678X'),
            'is_change_password' => 1
        ]);
        $admin->assignRole("admin");
    }
}
