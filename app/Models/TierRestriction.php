<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TierRestriction extends Model
{
    protected $fillable = [
        'merchant_id',
        'tier_id',
        'type',
        'restrictions',
    ];

    protected $casts = [
        'restrictions' => 'array',
    ];
}
