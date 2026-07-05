<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'product_name' => "Sofa Minimalis Premium L-Shape",
                'description'  => "Sofa modern dengan desain minimalis eropa, dilapisi kain fabric premium anti-panas dan rangka kayu solid kokoh.",
                'price'        => 2500000,
                'stock'        => 15,
                'image'        => "https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=600&q=80",
                'category_id'  => 1 // Pastikan ID ini ada di tabel categories
            ],
            [
                'product_name' => "Meja Kerja Ergonomis Jati",
                'description'  => "Meja kerja dari kayu jati pilihan dilengkapi lubang manajemen kabel untuk menjaga meja tetap rapi.",
                'price'        => 850000,
                'stock'        => 10,
                'image'        => "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=600&q=80",
                'category_id'  => 2
            ],
            [
                'product_name' => "Tempat Tidur King Size Minimalis",
                'description'  => "Rangka ranjang kokoh dengan desain headboard berlapis busa empuk demi kenyamanan istirahat maksimal.",
                'price'        => 4200000,
                'stock'        => 5,
                'image'        => "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80",
                'category_id'  => 3
            ],
            [
                'product_name' => "Kursi Makan Scandinavian",
                'description'  => "Kursi makan minimalis dengan kaki kayu beech solid dan dudukan polipropilen ergonomis.",
                'price'        => 350000,
                'stock'        => 20,
                'image'        => "https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?auto=format&fit=crop&w=600&q=80",
                'category_id'  => 4
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
