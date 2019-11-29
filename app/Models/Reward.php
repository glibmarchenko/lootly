<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Reward extends Authenticatable
{

    const TYPE_FIXED_AMOUNT = 'Fixed amount';
    const TYPE_VARIABLE_AMOUNT = 'Variable amount';
    const TYPE_PERCENT_OFF = 'Percentage off';
    const TYPE_FREE_SHIPPING = 'Free shipping';
    const TYPE_FREE_PRODUCT = 'Free Product';
    const TYPE_POINTS = 'Points';


    const CREATE_ORDER_REWARD = 1;
    protected $table = 'rewards';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'url', 'type', 'icon', 'description', 'display_order'
    ];

    public function merchantReward()
    {
        return $this->hasMany('App\Models\MerchantReward', 'reward_id', 'id');
    }
}
