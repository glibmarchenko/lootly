<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Merchant;
use App\Models\MerchantReward;
use App\Models\Order;

class Coupon extends Model
{
    protected $table = 'coupons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id',
        'customer_id',
        'merchant_reward_id',
        'shop_coupon_id',
        'coupon_code',
        'is_used',
        'created_by_customer_id',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function merchant_reward()
    {
        return $this->belongsTo(MerchantReward::class, 'merchant_reward_id');
    }


    public function order()
    {
        return $this->hasOne(Order::class, 'coupon_id');
    }

    public function creator()
    {
        return $this->belongsTo(Customer::class, 'created_by_customer_id');
    }
}
