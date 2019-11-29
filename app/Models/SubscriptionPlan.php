<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SubscriptionPlan extends Model
{


    protected $table = 'subscription_plans';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'name', 'price', 'trial_days'
    ];

}
