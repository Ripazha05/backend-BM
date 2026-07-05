<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::create([
            'id_user' => 2, // Mengacu ke Budi Pelanggan
            'order_date' => Carbon::now(),
            'total_price' => 15150000, // Harga laptop (15jt) + 2 Kaos (150rb)
            'shipping_address' => 'Jl. Merdeka No. 45, Bandung',
            'status' => 'pending',
        ]);
    }
}
