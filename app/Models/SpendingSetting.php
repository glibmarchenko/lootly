<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpendingSetting extends Model
{
    protected $fillable = [
        'title',
        'box_color',
        'box_font_size',
        'box_text_color',
        'title_color',
        'title_font_size',
    ];

    public function reward_setting()
    {
        return $this->belongsTo('App\Models\RewardSetting');
    }

    public function merchant_rewards()
    {
        return $this->hasMany('App\Models\MerchantReward', 'spending_settings_id');
    }
}
