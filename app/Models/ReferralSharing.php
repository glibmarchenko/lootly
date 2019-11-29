<?php

namespace App\Models;

use App\Merchant;
use Illuminate\Database\Eloquent\Model;

class ReferralSharing extends Model
{
    protected $table = 'referral_sharing';

    protected $fillable = [
        'merchant_id',
        'facebook_status',
        'facebook_message',
        'facebook_icon',
        'facebook_icon_name',
        'twitter_status',
        'twitter_message',
        'twitter_icon',
        'twitter_icon_name',
        'google_status',
        'google_message',
        'google_icon',
        'google_icon_name',
        'email_status',
        'email_subject',
        'email_body',
        'share_title',
        'share_description',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
