<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailNotification extends Model
{
    protected $casts = [
        'available_tags' => 'array'
    ];
}
