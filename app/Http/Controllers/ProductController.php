<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Fungsi menampilkan semua produk beserta kategorinya
    public function index()
    {
        // Menggunakan with() agar tabel relasi kategori ikut dikirim ke React
        $products = Product::with('category')->get();
        return response()->json($products, 200);
    }

    // Fungsi menyimpan produk baru
    public function store(Request $request)
    {
        try {
            // 1. Validasi data dari React
            $validatedData = $request->validate([
                'product_name' => 'required|string|max:255',
                'category_id'  => 'required|integer|exists:categories,id_categories',
                'price'        => 'required|integer',
                'stock'        => 'required|integer',
                'description'  => 'nullable|string',
            ]);

            // 2. Beri nilai default untuk image jika tidak ada
            $validatedData['image'] = $request->input('image', 'default.png');

            // 3. Simpan menggunakan Eloquent (Otomatis dilindungi $fillable)
            $product = Product::create($validatedData);

            return response()->json($product, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat menyimpan data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Fungsi mengupdate produk yang sudah ada
    public function update(Request $request, $id)
    {
        try {
            // Cari produk berdasarkan id_product, akan otomatis error 404 jika tidak ada
            $product = Product::findOrFail($id);

            // Validasi data
            $validatedData = $request->validate([
                'product_name' => 'sometimes|required|string|max:255',
                'category_id'  => 'sometimes|required|integer|exists:categories,id_categories',
                'price'        => 'sometimes|required|integer',
                'stock'        => 'sometimes|required|integer',
                'description'  => 'nullable|string',
                'image'        => 'nullable|string'
            ]);

            // Update menggunakan Eloquent
            $product->update($validatedData);

            return response()->json($product, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat memperbarui data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Fungsi menghapus produk
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json(['message' => 'Produk berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Produk tidak ditemukan atau gagal dihapus',
                'error'   => $e->getMessage()
            ], 404);
        }
    }
}
