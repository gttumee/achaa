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
        'payment_status',
        'payment_content',
        'logistic_type',
        'bairshil_id',
        'shipping_type',
        'shipping_date',
        'payed_date'
    ];

    public function bairshil()
{
    return $this->belongsTo(Bairshil::class);
}
}