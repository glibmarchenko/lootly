<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    protected $table = 'tiers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id',
        'name',
        'spend_value',
        'multiplier',
        'rolling_days',
        'image_url',
        'image_name',
        'text_email',
        'text_email_default',
        'status',
        'requirement_text',
        'requirement_text_default',
        'multiplier_text',
        'multiplier_text_default',
        'email_notification',
        'benefits_type',
        'benefits_discount',
        'benefits_reward',
        'currency',
        'restrictions_enabled',
        'default_icon_color',
    ];

    public function customer()
    {
        return $this->hasMany('App\Models\Customer', 'tier_id', 'id');
    }

    public function tierBenefits()
    {
        return $this->hasMany('App\Models\TierBenefit', 'tier_id', 'id');
    }
}
