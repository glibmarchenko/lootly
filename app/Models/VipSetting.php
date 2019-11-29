<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipSetting extends Model
{
    protected $fillable = [
        'title',
        'multiplier_color',
        'multiplier_font_size',
        'requirements_color',
        'requirements_font_size',
        'tier_name_color',
        'tier_name_font_size',
        'title_color',
        'title_font_size',
    ];

    public function reward_setting()
    {
        return $this->belongsTo('App\Models\RewardSetting');
    }
}
