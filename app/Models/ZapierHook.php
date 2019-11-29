<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZapierHook extends Model
{

    CONST EVENT_GIVE_POINTS = 'give_points';
    CONST EVENT_DEDUCT_POINTS = 'deduct_points';
    CONST EVENT_GIVE_REWARD = 'event_give_reward';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'url', 'event'
    ];

}
