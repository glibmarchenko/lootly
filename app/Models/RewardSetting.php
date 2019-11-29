<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardSetting extends Model
{
    protected $guarded = [];

    // Relations section
    public function faq()
    {
        return $this->hasOne("App\Models\FaqSetting", 'reward_settings_id');
    }

    public function earn()
    {
        return $this->hasOne("App\Models\EarnSetting", 'reward_settings_id');
    }

    public function header()
    {
        return $this->hasOne("App\Models\HeaderSetting", 'reward_settings_id');
    }

    public function how_it_works()
    {
        return $this->hasOne("App\Models\HowItWorksSetting", 'reward_settings_id');
    }

    public function referral()
    {
        return $this->hasOne("App\Models\ReferralDisplaySetting", 'reward_settings_id');
    }

    public function spending()
    {
        return $this->hasOne("App\Models\SpendingSetting", 'reward_settings_id');
    }

    public function vip()
    {
        return $this->hasOne("App\Models\VipSetting", 'reward_settings_id');
    }

    public function merchant()
    {
        return $this->belongsTo("App\Merchant", 'merchant_id');
    }
    // End relations section
}