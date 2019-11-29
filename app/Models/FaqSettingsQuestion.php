<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqSettingsQuestion extends Model
{
    protected $fillable = [
                            'question',
                            'answer',
                        ];
    
    public function faq_setting()
    {
        return $this->belongsTo('App\Models\FaqSetting', 'faq_settings_id');
    }
}