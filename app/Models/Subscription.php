<?php

namespace App\Models;

use App\User;
use App\Merchant;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    const STATUS_ACTIVE = 'active';

    const STATUS_TRIALING = 'trialing';

    const STATUS_CANCELLED = 'cancelled';

    protected $table = 'subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'merchant_id',
        'name',
        'stripe_customer_id',
        'stripe_product_id',
        'stripe_id',
        'stripe_plan',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'length',
        'status',
        'shopify_id',
        'plan_id',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'ends_at',
        'trial_ends_at',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function getType()
    {
        if ($this->length == 'year') {
            return 'Yearly';
        } else {
            return 'Monthly';
        }
    }

    public function isActive()
    {
        if ($this->status === self::STATUS_ACTIVE) {
            return true;
        }

        return false;
    }

    public function isTrial()
    {
        $date = Carbon::now()->getTimestamp();

        if ($this->status === self::STATUS_TRIALING && $this->trial_ends_at && $this->trial_ends_at->timestamp >= $date) {
            return true;
        }

        return false;
    }
}
