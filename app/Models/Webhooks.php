<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Webhooks extends Model
{



    protected $table = 'webhooks';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'webhook_id', 'topic', 'address'
    ];

}
