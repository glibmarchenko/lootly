<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'merchant_id',
        'name',
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_tags')->withPivot('customer_id');
    }
}
