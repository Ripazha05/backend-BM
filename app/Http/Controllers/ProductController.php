<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Tampilkan semua produk beserta kategorinya
    public function index()
    {
        $products = Product::with('category')->get();
        return response()->json($products, 200);
    }

    // Menyimpan produk baru dengan upload gambar
    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_name' => 'required|string|max:255',
                'category_id'  => 'required|integer|exists:categories,id_categories',
                'price'        => 'required|integer',
                'stock'        => 'required|integer',
                'description'  => 'nullable|string',
                'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Maksimal 5MB
            ]);

            $imageName = 'default.png';

            // Proses upload jika file gambar dikirim
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->storeAs('products', $imageName, 'public');
            }

            $product = Product::create([
                'product_name' => $request->product_name,
                'category_id'  => $request->category_id,
                'price'        => $request->price,
                'stock'        => $request->stock,
                'description'  => $request->description,
                'image'        => $imageName,
            ]);

            // Load relasi kategori agar state di React langsung dapat nama kategorinya
            $product->load('category');

            return response()->json($product, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat menyimpan data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Mengupdate produk (mendukung update teks & gambar)
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $request->validate([
                'product_name' => 'sometimes|required|string|max:255',
                'category_id'  => 'sometimes|required|integer|exists:categories,id_categories',
                'price'        => 'sometimes|required|integer',
                'stock'        => 'sometimes|required|integer',
                'description'  => 'nullable|string',
                'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120'
            ]);

            $dataToUpdate = $request->except('image');

            // Jika ada file gambar baru yang diupload
            if ($request->hasFile('image')) {
                // Hapus gambar lama jika bukan default.png
                if ($product->image && $product->image !== 'default.png') {
                    Storage::disk('public')->delete('products/' . $product->image);
                }

                $file = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $file->storeAs('products', $imageName, 'public');

                $dataToUpdate['image'] = $imageName;
            }

            $product->update($dataToUpdate);
            $product->load('category'); // Reload relasi

            return response()->json($product, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat memperbarui data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // Menghapus produk beserta file gambarnya
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Hapus fisik file gambar di server jika ada
            if ($product->image && $product->image !== 'default.png') {
                Storage::disk('public')->delete('products/' . $product->image);
            }

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