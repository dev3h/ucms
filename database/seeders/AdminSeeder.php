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
        $user = User::create([
            "name" => "nam nd",
            "email" => "namnd@yopmail.com",
            'password' => bcrypt('a12345678X'),
            'is_change_password' => 1
        ]);
        $user->assignRole("master admin");
        $user->syncPermissions($user->roles->first()->permissions);
    }
}
