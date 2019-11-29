<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Merchant;

class NotificationSettings extends Model
{
    const AVAILABLE_TYPES = [
        'points' => [
            'earned',
            'spent',
            'reward_available',
            'point_expiration',
            'vip_tier_earned'
        ],
        'referral' => [
            'share_email',
            'receiver_reward',
            'sender_reward'
        ]
    ];

    protected $fillable = [
        'subject',
        'body',
        'button_text',
        'button_color',
        'notification_type',
    ];

    protected $casts = [
        'icons' => 'array'
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
