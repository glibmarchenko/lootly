<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{

    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'customer_id', 'total_price', 'refunded_total', 'referral_slug', 'referring_customer_id', 'coupon_id', 'status'
    ];

    public function calcPrice(){
        return $this->total_price - $this->refunded_total;
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id')->withDefault();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function referral()
    {
        return $this->belongsTo(Customer::class, 'referring_customer_id');
    }

}
