<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserRole extends Model
{



    protected $table = 'role_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'role_id'
    ];
}
