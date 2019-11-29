<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantReward extends Model
{
    use SoftDeletes;

    const REWARD_TYPE_POINT = 1;

    const REWARD_TYPE_REFERRAL_SENDER = 2;

    const REWARD_TYPE_REFERRAL_RECEIVER = 3;

    protected $table = 'merchant_rewards';

    const SPENDING_LIMIT_TYPES = [
        'times',
        'points',
    ];

    const SPENDING_LIMIT_PERIODS = [
        'lifetime',
        'month',
        'week',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reward_id',
        'merchant_id',
        'type_id',
        'rewardDefaultText',
        'coupon_expiration',
        'coupon_expiration_time',
        'rewardDefaultName',
        'reward_icon',
        'reward_email_text',
        'reward_type',
        'points_required',
        'reward_value',
        'variable_reward_value',
        'variable_point_cost',
        'variable_point_min',
        'variable_point_max',
        'coupon_prefix',
        'zap_status',
        'zap_key',
        'order_minimum',
        'spending_limit',
        'spending_limit_value',
        'spending_limit_type',
        'spending_limit_period',
        'category_id',
        'product',
        'send_email_notification',
        'active_flag',
        'reward_icon_name',
        'max_shipping',
        'restrictions_enabled'
    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['reward_display_name'];

    public function getRewardDisplayNameAttribute()
    {
        return (in_array($this->type_id, [
            self::REWARD_TYPE_REFERRAL_RECEIVER,
            self::REWARD_TYPE_REFERRAL_SENDER,
        ])) ? $this->reward_text : $this->reward_name;
    }

    public function merchant()
    {
        return $this->hasOne('App\Merchant', 'merchant_id');
    }

    public function reward()
    {
        return $this->belongsTo('App\Models\Reward', 'reward_id');
    }

    public function point()
    {
        return $this->hasMany('App\Models\Point', 'merchant_reward_id');
    }

    public function getTypeId($type)
    {
        $type_id = null;

        switch ($type) {
            case 'point':
                $type_id = self::REWARD_TYPE_POINT;
                break;
            case 'referral_sender':
                $type_id = self::REWARD_TYPE_REFERRAL_SENDER;
                break;
            case 'referral_receiver':
                $type_id = self::REWARD_TYPE_REFERRAL_RECEIVER;
                break;
        }

        return $type_id;
    }

    public function spending_setting()
    {
        return $this->belongsTo('App\Models\SpendingSetting', 'spending_settings_id');
    }

    public function coupons()
    {
        return $this->hasMany('App\Models\Coupon', 'merchant_reward_id');
    }
}
