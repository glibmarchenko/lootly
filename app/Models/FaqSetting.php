<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqSetting extends Model
{
    protected $fillable = [
                            'status', 
                            'title',
                            'answer_color',
                            'answer_font_size',
                            'question_color',
                            'question_font_size',
                            'title_color',
                            'title_font_size'
                        ];

    public function reward_setting()
    {
        return $this->belongsTo('App\Models\RewardSetting');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\FaqSettingsQuestion', 'faq_settings_id');
    }
}