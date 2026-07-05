<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Fungsi menampilkan semua produk
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    // Fungsi menyimpan produk baru
    public function store(Request $request)
    {
        try {
            // Ambil data yang dikirim dari React dengan fallback (nilai cadangan jika nama variable beda)
            $productName = $request->input('product_name') ?? $request->input('namaProduk');
            $categoryId  = $request->input('category_id') ?? $request->input('kategori');
            $price       = $request->input('price') ?? $request->input('harga');
            $stock       = $request->input('stock') ?? $request->input('stok');
            $description = $request->input('description') ?? $request->input('deskripsi') ?? '-';

            // Paksa simpan data langsung menggunakan Query Builder untuk melewati proteksi $fillable sementara waktu
            $id = \DB::table('products')->insertGetId([
                'product_name' => $productName,
                'category_id'  => $categoryId,
                'price'        => $price,
                'stock'        => $stock,
                'description'  => $description,
                'image'        => 'default.png', // Memberikan nilai default secara paksa agar database menerima
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            // Ambil data yang baru saja dimasukkan
            $product = \DB::table('products')->where('id_product', $id)->first();

            return response()->json($product, 201);

        } catch (\Exception $e) {
            // Jika ada masalah lain, return pesan error agar bisa dibaca langsung di React Console Log
            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Fungsi mengupdate produk yang sudah ada
    public function update(Request $request, $id)
    {
        try {
            $product = \DB::table('products')->where('id_product', $id)->first();

            if (!$product) {
                return response()->json(['message' => 'Produk tidak ditemukan'], 404);
            }

            // Ambil data yang dikirim dari React dengan fallback (sama seperti store())
            $productName = $request->input('product_name') ?? $request->input('namaProduk');
            $categoryId  = $request->input('category_id') ?? $request->input('kategori');
            $price       = $request->input('price') ?? $request->input('harga');
            $stock       = $request->input('stock') ?? $request->input('stok');
            $description = $request->input('description') ?? $request->input('deskripsi') ?? '-';

            \DB::table('products')->where('id_product', $id)->update([
                'product_name' => $productName,
                'category_id'  => $categoryId,
                'price'        => $price,
                'stock'        => $stock,
                'description'  => $description,
                'updated_at'   => now(),
            ]);

            $updated = \DB::table('products')->where('id_product', $id)->first();

            return response()->json($updated, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Fungsi menghapus produk
    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Produk berhasil dihapus'], 200);
        }
        return response()->json(['message' => 'Produk tidak ditemukan'], 404);
    }
}
