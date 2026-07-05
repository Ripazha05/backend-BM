<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_payment');
            $table->unsignedBigInteger('id_order');
            $table->string('sender_name');
            $table->string('bank_name');
            $table->dateTime('payment_date');
            $table->string('proof_image')->nullable();
            $table->string('payment_status')->default('pending'); // contoh: pending, verified, rejected
            $table->timestamps();

            // Relasi Foreign Key ke orders
            $table->foreign('id_order')
                  ->references('id_order')
                  ->on('orders')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
