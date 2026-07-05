<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Utama',
                'password' => bcrypt('admin123'),
                'role' => 'Admin'
            ]
        );

        // Manager
        User::firstOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Manager Operasional',
                'password' => bcrypt('manager123'),
                'role' => 'Manager'
            ]
        );

        // Staff
        User::firstOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff Gudang',
                'password' => bcrypt('staff123'),
                'role' => 'Staff'
            ]
        );

        // Categories
        $categories = ['Elektronik', 'Furniture', 'Kendaraan'];
        foreach ($categories as $cat) {
            \App\Models\Category::firstOrCreate(['name' => $cat]);
        }
    }
}
