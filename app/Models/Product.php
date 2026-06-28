<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id_product';

    protected $fillable = ['product_name', 'description', 'price', 'stock', 'image', 'category_id'];
}
