<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $table = 'referrals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'referral_customer_id',
        // Customer ID who gave referral
        'invited_customer_id',
        // Customer ID who received referral
    ];

    public function referrer_customer()
    {
        return $this->belongsTo(Customer::class, 'referral_customer_id');
    }

    public function referred_customer()
    {
        return $this->belongsTo(Customer::class, 'invited_customer_id');
    }
}