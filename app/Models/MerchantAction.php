<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantAction extends Model
{
    protected $table = 'merchant_actions';

    const GOAL_UNITS = [
        'order',
        'money',
    ];

    const EARNING_LIMIT_TYPES = [
        'times',
        'points',
    ];

    const EARNING_LIMIT_PERIODS = [
        'lifetime',
        'year',
        'month',
        'week',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'earn_settings_id',
        'action_id',
        'goal',
        'goal_unit',
        'merchant_id',
        'action_name',
        'action_icon',
        'reward_text',
        'reward_default_text',
        'reward_email_text',
        'point_value',
        'option_1',
        'option_2',
        'earning_limit',
        'earning_limit_value',
        'earning_limit_type',
        'earning_limit_period',
        'active_flag',
        'send_email_notification',
        'is_fixed',
        'fb_page_url',
        'share_url',
        'share_message',
        'twitter_username',
        'instagram_username',
        'zap_name',
        'content_url',
        'review_type',
        'review_status',
        'action_icon_name',
        'restrictions_enabled'
    ];

    public function merchant()
    {
        return $this->belongsTo('App\Merchant', 'merchant_id');
    }

    public function action()
    {
        return $this->belongsTo('App\Models\Action', 'action_id');
    }

    public function point()
    {
        return $this->hasMany('App\Models\Point', 'merchant_action_id');
    }

    public function earning_settings()
    {
        return $this->belongsTo('App\Models\EarnSetting', 'earn_settings_id');
    }
}
