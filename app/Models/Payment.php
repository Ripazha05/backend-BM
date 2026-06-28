<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id_payment';

    protected $fillable = ['id_order', 'sender_name', 'bank_name', 'payment_date', 'proof_image', 'payment_status'];
}
