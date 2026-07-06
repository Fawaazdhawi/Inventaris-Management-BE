<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'details.product']);
        
        // If staff/manager, they can see all. If we want to restrict, we can here.
        // Let's assume all users can see history, or at least their own history.
        // The spec says Manager can "Melihat laporan", Staff "Kelola data inventaris".
        if ($request->user()->role !== 'Admin' && $request->user()->role !== 'Manager' && $request->user()->role !== 'Staff') {
             // For regular users (if any), only show their own. But we only have Admin, Staff, Manager roles.
        }

        $borrowings = $query->latest()->paginate(10);
        return response()->json($borrowings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pinjam' => 'required|date',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id'
        ]);

        DB::beginTransaction();
        try {
            $borrowing = Borrowing::create([
                'user_id' => $request->user()->id,
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'status' => 'dipinjam'
            ]);

            foreach ($validated['product_ids'] as $productId) {
                $product = Product::findOrFail($productId);
                if ($product->stok < 1) {
                    throw new \Exception("Stok tidak mencukupi untuk barang: " . $product->nama_barang);
                }
                
                $product->decrement('stok');

                if ($product->stok < 5) {
                    event(new \App\Events\LowStockNotification($product));
                }

                BorrowingDetail::create([
                    'borrowing_id' => $borrowing->id,
                    'product_id' => $product->id
                ]);
            }

            DB::commit();
            return response()->json($borrowing->load('details.product'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function return(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return response()->json(['message' => 'Sudah dikembalikan'], 400);
        }

        $validated = $request->validate([
            'tanggal_kembali' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            $borrowing->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => $validated['tanggal_kembali']
            ]);

            foreach ($borrowing->details as $detail) {
                $detail->product->increment('stok');
            }

            DB::commit();
            return response()->json($borrowing);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
