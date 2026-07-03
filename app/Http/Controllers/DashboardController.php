<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Total Barang (sum of all stocks or count of unique products?)
        // Assuming total distinct products
        $totalProducts = Product::count();
        $totalStock = Product::sum('stok');

        // 2. Barang Dipinjam (count of items currently borrowed)
        $borrowedItemsCount = DB::table('borrowing_details')
            ->join('borrowings', 'borrowing_details.borrowing_id', '=', 'borrowings.id')
            ->where('borrowings.status', 'dipinjam')
            ->count();

        // 3. Barang Tersedia
        $availableItemsCount = $totalStock; // Because we decrement stock when borrowed

        // 4. Grafik Peminjaman per Bulan (in current year)
        $currentYear = Carbon::now()->year;
        $monthlyBorrowings = Borrowing::select(
                DB::raw('extract(month from tanggal_pinjam) as month'),
                DB::raw('count(*) as total')
            )
            ->whereYear('tanggal_pinjam', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'total_barang' => $totalProducts,
            'total_stok' => $totalStock,
            'barang_dipinjam' => $borrowedItemsCount,
            'barang_tersedia' => $availableItemsCount,
            'grafik_peminjaman_bulanan' => $monthlyBorrowings
        ]);
    }
}
