<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Merchant;

class Point extends Model
{
    const TYPE_ADMIN = 'Admin';

    const TITLE_ADMIN = 'Admin';
    const TITLE_ORDER_REFUND = 'Order Refund';

    protected $table = 'points';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id', 'customer_id', 'point_value', 'merchant_action_id', 'merchant_reward_id', 'coupon_id', 'reason', 'title',
        'order_id', 'total_order_amount', 'rewardable_order_amount', 'type', 'expiration_date', 'tier_multiplier', 'referral_id'
    ];

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'id', 'merchant_id')->withDefault();
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id')->withDefault();
    }

    public function coupon()
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id')->withDefault();
    }

    public function action()
    {
        return $this->belongsTo(MerchantAction::class, 'merchant_action_id');
    }

    public function reward()
    {
        return $this->hasOne(MerchantReward::class, 'id', 'merchant_reward_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function getActionName()
    {
        if ($this->title !== self::TITLE_ORDER_REFUND) {
            if ($this->merchant_action_id) {
                return $this->action->action_name;

            } elseif ($this->merchant_reward_id) {
                return $this->reward->reward_name;

            } elseif ($this->title === self::TITLE_ADMIN && $this->reason) {
                return $this->reason;

            } else {
                return $this->title;
            }

        } else {
            return $this->title;
        }
    }

    public function isAdminAction()
    {
        if ($this->type === self::TYPE_ADMIN) {
            return true;
        }
        return false;
    }
}
