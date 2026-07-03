<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(10);
        return response()->json($products);
    }

    public function store(Request $request)
    {
        // Only Admin or Staff can add
        if (!in_array($request->user()->role->name, ['Admin', 'Staff'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'kode_barang' => 'required|unique:products',
            'nama_barang' => 'required',
            'category_id' => 'required|exists:categories,id',
            'stok' => 'required|integer|min:0',
            'lokasi_penyimpanan' => 'required',
            'kondisi_barang' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/products');
            $validated['image'] = Storage::url($path);
        }

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    public function update(Request $request, Product $product)
    {
        if (!in_array($request->user()->role->name, ['Admin', 'Staff'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'kode_barang' => 'sometimes|unique:products,kode_barang,' . $product->id,
            'nama_barang' => 'sometimes',
            'category_id' => 'sometimes|exists:categories,id',
            'stok' => 'sometimes|integer|min:0',
            'lokasi_penyimpanan' => 'sometimes',
            'kondisi_barang' => 'sometimes',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/products');
            $validated['image'] = Storage::url($path);
        }

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(Request $request, Product $product)
    {
        if (!in_array($request->user()->role->name, ['Admin', 'Staff'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();
        return response()->json(['message' => 'Barang dihapus']);
    }
}
