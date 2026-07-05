<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create([
            'category_name' => 'Sofa',
            'description' => 'Berbagai macam sofa yang nyaman untuk ruang tamu dan keluarga',
        ]);

        Category::create([
            'category_name' => 'Meja Kerja',
            'description' => 'Meja kerja ergonomis dan estetik untuk produktivitas',
        ]);

        Category::create([
            'category_name' => 'Tempat Tidur',
            'description' => 'Ranjang dan tempat tidur yang nyaman untuk istirahat maksimal',
        ]);

        Category::create([
            'category_name' => 'Kursi',
            'description' => 'Kursi makan, kursi santai, dan kursi tamu',
        ]);
    }
}
