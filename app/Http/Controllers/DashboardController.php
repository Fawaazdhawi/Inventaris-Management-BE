<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Borrowing;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $total_barang = Product::count();
        $barang_dipinjam = Borrowing::where('status', 'dipinjam')->count();
        $barang_tersedia = Product::sum('stok');
        
        $grafik = Borrowing::select(
            DB::raw('EXTRACT(MONTH FROM tanggal_pinjam) as month'),
            DB::raw('count(*) as total')
        )
        ->whereYear('tanggal_pinjam', date('Y'))
        ->groupBy('month')
        ->get();

        return response()->json([
            'total_barang' => $total_barang,
            'barang_dipinjam' => $barang_dipinjam,
            'barang_tersedia' => $barang_tersedia,
            'grafik_peminjaman_bulanan' => $grafik
        ]);
    }
}
