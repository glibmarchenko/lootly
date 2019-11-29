<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantRewardRestriction extends Model
{
    protected $fillable = [
        'merchant_id',
        'merchant_reward_id',
        'type',
        'restrictions'
    ];

    protected $casts = [
        'restrictions' => 'array'
    ];
}
