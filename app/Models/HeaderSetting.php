<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeaderSetting extends Model
{
    protected $fillable = [
                            'title',
                            'subtitle',
                            'background',
                            'background_name',
                            'background_opacity',
                            'button1_text',
                            'button1_link',
                            'button2_text',
                            'button2_link',
                            'button_color',
                            'button_font_size',
                            'button_text_color',
                            'header_color',
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
