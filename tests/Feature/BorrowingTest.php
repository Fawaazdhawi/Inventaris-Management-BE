<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Manager']);
        Role::create(['name' => 'Staff']);
        
        Category::create(['name' => 'Elektronik']);
    }

    public function test_staff_can_borrow_product()
    {
        $staffRole = Role::where('name', 'Staff')->first();
        $staff = User::factory()->create(['role_id' => $staffRole->id]);
        
        $product = Product::create([
            'kode_barang' => 'BRG-010',
            'nama_barang' => 'Proyektor EPSON',
            'kategori_id' => Category::first()->id,
            'stok' => 5,
            'lokasi_penyimpanan' => 'Ruang Meeting',
            'kondisi_barang' => 'Baik'
        ]);

        $response = $this->actingAs($staff)->postJson('/api/borrowings', [
            'nama_peminjam' => 'Budi Santoso',
            'product_ids' => [$product->id],
            'tanggal_pinjam' => now()->toDateString(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('borrowings', [
            'nama_peminjam' => 'Budi Santoso',
            'status' => 'dipinjam'
        ]);
        
        // Assert that stock is decreased
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok' => 4
        ]);
    }

    public function test_admin_can_return_borrowed_product()
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        
        $product = Product::create([
            'kode_barang' => 'BRG-011',
            'nama_barang' => 'Kamera Canon',
            'kategori_id' => Category::first()->id,
            'stok' => 2, // Stock when borrowed is technically less, let's say it's 2 currently
            'lokasi_penyimpanan' => 'Gudang C',
            'kondisi_barang' => 'Baik'
        ]);

        $borrowing = Borrowing::create([
            'user_id' => $admin->id,
            'nama_peminjam' => 'Siti Aminah',
            'tanggal_pinjam' => now()->subDays(2)->toDateString(),
            'status' => 'dipinjam'
        ]);
        
        \App\Models\BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($admin)->postJson("/api/borrowings/{$borrowing->id}/return", [
            'tanggal_kembali' => now()->toDateString()
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'dikembalikan'
        ]);
        
        // Assert that stock is increased
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stok' => 3
        ]);
    }
}
