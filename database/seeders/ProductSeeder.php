<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'product_name' => 'Laptop Asus ROG',
            'description' => 'Laptop gaming spesifikasi tinggi',
            'price' => 15000000,
            'stock' => 10,
            'category_id' => 1, // Mengacu ke Elektronik
        ]);

        Product::create([
            'product_name' => 'Kaos Polos Katun',
            'description' => 'Kaos bahan katun bambu super nyaman',
            'price' => 75000,
            'stock' => 50,
            'category_id' => 2, // Mengacu ke Pakaian
        ]);
    }
}
