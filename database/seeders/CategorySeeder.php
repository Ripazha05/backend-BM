<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::create([
            'category_name' => 'Elektronik',
            'description' => 'Gadget, Laptop, dan alat elektronik lainnya',
        ]);

        Category::create([
            'category_name' => 'Pakaian',
            'description' => 'Baju, Celana, Jaket pria dan wanita',
        ]);
    }
}
