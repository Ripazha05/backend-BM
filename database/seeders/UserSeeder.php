<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@toko.com',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Budi Pelanggan',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '089876543210',
            'address' => 'Jl. Merdeka No. 45, Bandung',
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Ripa Geming',
            'email' => 'ripantat@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '089053572180',
            'address' => 'Jl. Duri No. 67, Rumbai',
            'role' => 'owner',
        ]);
    }
}
