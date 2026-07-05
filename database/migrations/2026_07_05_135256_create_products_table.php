<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->integer('price'); // Bisa diganti decimal('price', 12, 2) jika butuh sen
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            // Relasi Foreign Key ke categories
            $table->foreign('category_id')
                  ->references('id_categories')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
