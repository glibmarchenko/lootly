<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantUser extends Model
{
    protected $table = 'merchant_users';

    protected $fillable = [
        'user_id', 'merchant_id', 'role'
    ];
}
