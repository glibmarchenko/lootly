<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


class Invitation extends \Laravel\Spark\Invitation
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /*protected $fillable = [
        'merchant_id', 'user_id', 'access', 'name', 'status', 'email', 'token'
    ];*/

    /*public function user()
    {
        return $this->hasOne(User::class, 'email', 'email')->withDefault();
    }*/

}
