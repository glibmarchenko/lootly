<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierBenefit extends Model
{
    protected $table = 'tier_benefits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tier_id',
        'benefits_type',
        'benefits_discount',
        'benefits_reward',
        'merchant_reward_id',
    ];

    protected $appends = [
        'benefits_discount_text',
    ];

    public function getRewardName()
    {
        if (empty($this->merchant_reward_id)) {
            return $this->benefits_discount;
        } else {
            return $this->reward->reward_name;
        }
    }

    public function reward()
    {
        return $this->belongsTo('App\Models\MerchantReward', 'merchant_reward_id');
    }

    public function tier()
    {
        return $this->belongsTo('App\Models\Tier', 'tier_id');
    }

    public function getBenefitsDiscountTextAttribute()
    {
        switch ($this->benefits_reward) {
            case 'Free Shipping':
                return $this->benefits_reward.' '.$this->benefits_discount.' Amount';
                break;
            case 'Free Product':
                return $this->benefits_reward.' '.$this->benefits_discount;
                break;
            case 'points':
                return $this->benefits_discount.' Free Points';
                break;
            default:
                return $this->getRewardName();
                break;
        }
    }
}
