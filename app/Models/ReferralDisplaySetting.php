<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralDisplaySetting extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'subtitle_color',
        'subtitle_font_size',
        'title_color',
        'title_font_size',
    ];

    public function reward_setting()
    {
        return $this->belongsTo('App\Models\RewardSetting');
    }
}