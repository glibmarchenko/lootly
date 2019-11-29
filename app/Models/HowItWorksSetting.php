<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HowItWorksSetting extends Model
{
    protected $fillable = [
                            'title',
                            'steep1_text',
                            'steep2_text',
                            'steep3_text',
                            'arrows_color',
                            'circle_empty_color',
                            'circle_full_color',
                            'steps_front_size',
                            'title_color',
                            'title_font_size',
                        ];

    public function reward_setting()
    {
        return $this->belongsTo('App\Models\RewardSetting');
    }
}