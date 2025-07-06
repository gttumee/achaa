<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
      protected $fillable = [
        'transfer_cost',
        'phone',
        'second_phone',
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
        'payed_date',
        'user_id'

    ];

    public function bairshil()
{
    return $this->belongsTo(Bairshil::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
}