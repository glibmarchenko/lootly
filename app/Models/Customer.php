<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Merchant;

class Customer extends Authenticatable
{
    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id',
        'name',
        'email',
        'country',
        'zipcode',
        'birthday',
        'referral_slug',
        'tier_id',
        'ecommerce_id',
        'orders_count',
        'shares_count',
        'clicks_count',
        'lock_points',
        'last_available_reward_id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }

    public function referral_orders()
    {
        return $this->hasMany(Order::class, 'referring_customer_id');
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'customer_id', 'id');
    }

    public function earned_points()
    {
        return $this->points();
    }

    public function earned_points_in_year()
    {
        return $this->points();
    }

    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'tier_id');
    }

    public function tier_history()
    {
        return $this->hasMany(TierHistory::class, 'customer_id');
    }

    public function coupons()
    {
        return $this->hasMany('App\Models\Coupon', 'customer_id', 'id');
    }

    public function created_coupons()
    {
        return $this->hasMany('App\Models\Coupon', 'created_by_customer_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'customer_tags')->withPivot('tag_id');
    }

    public function tags_pivot()
    {
        return $this->hasMany(CustomerTag::class, 'customer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function currency()
    {
        return $this->merchant->merchant_currency;
    }

    // Customer who invited that customer
    public function referrer()
    {
        return $this->belongsToMany(Customer::class, 'referrals', 'invited_customer_id', 'referral_customer_id');
    }

    // Customers who were invited by that customer
    public function referred()
    {
        return $this->belongsToMany(Customer::class, 'referrals', 'referral_customer_id', 'invited_customer_id');
    }

    public function transactionFlag()
    {
        return $this->hasOne(CustomerTransactionFlag::class, 'customer_id');
    }

    public function getTierName()
    {
        if (empty($this->tier)) {
            return '';
        }
        if (\is_array($this->tier)) {
            return $this->tier[0]->name;
        }

        return $this->tier->name;
    }

    public function clicks()
    {
        return $this->hasMany(CustomerReferralClick::class, 'customer_id');
    }

    public function shares()
    {
        return $this->hasMany(CustomerReferralShare::class, 'customer_id');
    }

    public function getLastOrdered()
    {
        $lastOrdered = $this->orders->sortByDesc('created_at')->first();
        if (empty($lastOrdered)) {
            $lastOrdered = new Order;
        }

        return $lastOrdered;
    }

    public function getBirthday()
    {
        if (! $this->birthday) {
            return 'N/A';
        }
        if ($this->birthday == '0000-00-00') {
            return 'N/A';
        }

        return $this->birthday;
    }
}
