<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    public function run()
    {
        Payment::create([
            'id_order' => 1,
            'sender_name' => 'Budi Santoso',
            'bank_name' => 'BCA',
            'payment_date' => Carbon::now(),
            'payment_status' => 'verified',
        ]);
    }
}
