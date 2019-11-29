<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscripsion extends Model
{
    protected $table = 'user_subscriptions';

    protected $hidden = ['merchant'];

    public function merchant(){
        return $this->belongsTo('App\Merchant');
    }

    public function plan(){
        return $this->belongsTo('App\Models\Plan');
    }
}
