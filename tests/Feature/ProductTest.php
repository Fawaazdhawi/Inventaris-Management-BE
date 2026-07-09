<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Manager']);
        Role::create(['name' => 'Staff']);
        
        // Create a default category
        Category::create(['name' => 'Elektronik']);
    }

    public function test_admin_can_create_product()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $category = Category::first();

        $response = $this->actingAs($admin)->postJson('/api/products', [
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Laptop ASUS',
            'category_id' => $category->id,
            'stok' => 10,
            'lokasi_penyimpanan' => 'Gudang A',
            'kondisi_barang' => 'Baik'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'kode_barang' => 'BRG-001',
            'nama_barang' => 'Laptop ASUS'
        ]);
    }

    public function test_manager_cannot_create_product()
    {
        $managerRole = Role::where('name', 'Manager')->first();
        $manager = User::factory()->create(['role_id' => $managerRole->id]);
        $category = Category::first();

        $response = $this->actingAs($manager)->postJson('/api/products', [
            'kode_barang' => 'BRG-002',
            'nama_barang' => 'Mouse Logitech',
            'category_id' => $category->id,
            'stok' => 20,
            'lokasi_penyimpanan' => 'Gudang B',
            'kondisi_barang' => 'Baik'
        ]);

        // Manager does not have authorization (middleware role:Admin,Staff)
        $response->assertStatus(403);
    }

    public function test_can_get_paginated_products()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        
        $category = Category::first();
        Product::create([
            'kode_barang' => 'BRG-003',
            'nama_barang' => 'Keyboard',
            'category_id' => $category->id,
            'stok' => 15,
            'lokasi_penyimpanan' => 'Gudang A',
            'kondisi_barang' => 'Baik'
        ]);

        $response = $this->actingAs($admin)->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'kode_barang', 'nama_barang', 'stok']
                     ],
                     'current_page',
                     'last_page'
                 ]);
    }
}
