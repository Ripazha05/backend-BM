<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderDetail;

class OrderDetailSeeder extends Seeder
{
    public function run()
    {
        // Membeli 1 Laptop
        OrderDetail::create([
            'id_order' => 1,
            'id_product' => 1, // Laptop Asus ROG
            'quantity' => 1,
            'price' => 15000000,
            'subtotal' => 15000000,
        ]);

        // Membeli 2 Kaos
        OrderDetail::create([
            'id_order' => 1,
            'id_product' => 2, // Kaos Polos Katun
            'quantity' => 2,
            'price' => 75000,
            'subtotal' => 150000,
        ]);
    }
}
