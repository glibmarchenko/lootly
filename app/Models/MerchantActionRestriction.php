<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantActionRestriction extends Model
{
    protected $fillable = [
        'merchant_id',
        'merchant_action_id',
        'type',
        'restrictions'
    ];

    protected $casts = [
        'restrictions' => 'array'
    ];
}
