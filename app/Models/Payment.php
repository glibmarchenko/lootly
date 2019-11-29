<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';

    protected $fillable = [
        'merchant_id',
        'user_id',
        'service',
        'payment_id',
        'status',
        'price',
        'plan_id',
        'type'
    ];
}
