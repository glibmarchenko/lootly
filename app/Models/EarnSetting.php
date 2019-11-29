<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EarnSetting extends Model
{
    protected $fillable = [
                            'title',
                            'action_font_size',
                            'action_text_color',
                            'box_color',
                            'point_color', 
                            'point_font_size',
                            'ribbon_color',
                            'title_color', 
                            'title_font_size',
                        ];
    
    // Relations section
    public function reward_setting()
    {
        return $this->belongsTo('App\Models\RewardSetting');
    }

    public function merchant_actions()
    {
        return $this->hasMany('App\Models\MerchantAction', 'earn_settings_id');
    }
    // End relations section
}