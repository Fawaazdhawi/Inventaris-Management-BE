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
        // Roles
        $roleAdmin = \App\Models\Role::firstOrCreate(['name' => 'Admin']);
        $roleManager = \App\Models\Role::firstOrCreate(['name' => 'Manager']);
        $roleStaff = \App\Models\Role::firstOrCreate(['name' => 'Staff']);

        // Admin
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Utama',
                'password' => bcrypt('admin123'),
                'role_id' => $roleAdmin->id
            ]
        );

        // Manager
        User::firstOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Manager Operasional',
                'password' => bcrypt('manager123'),
                'role_id' => $roleManager->id
            ]
        );

        // Staff
        User::firstOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff Gudang',
                'password' => bcrypt('staff123'),
                'role_id' => $roleStaff->id
            ]
        );

        // Categories
        $categories = ['Elektronik', 'Furniture', 'Kendaraan'];
        foreach ($categories as $cat) {
            \App\Models\Category::firstOrCreate(['name' => $cat]);
        }
    }
}
