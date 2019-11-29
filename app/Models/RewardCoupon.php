<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardCoupon extends Model
{
    const STATUS_AVAILABLE = 0;
    const STATUS_REDEEMED = 1;
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_reward_id', 'code', 'status'
    ];
}
