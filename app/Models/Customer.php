<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
      protected $fillable = [
        'name',
        'phone',
        'second_phone',
        'aimag',
        'sum',
        'content',
        'add_content',
        'pay',
        'payment_type'
    ];
}