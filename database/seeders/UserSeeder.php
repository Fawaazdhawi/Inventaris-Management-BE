<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role_id' => 1 // Admin
        ]);

        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@staff.com',
            'password' => Hash::make('password'),
            'role_id' => 2 // Staff
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@manager.com',
            'password' => Hash::make('password'),
            'role_id' => 3 // Manager
        ]);
    }
}
