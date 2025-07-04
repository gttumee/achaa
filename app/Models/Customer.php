<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
      protected $fillable = [
        'transfer_cost',
        'phone',
        'second_phone',
        'aimag',
        'sum',
        'content',
        'add_content',
        'pay',
        'status',
        'payment_type',
        'payment_status'
    ];
}