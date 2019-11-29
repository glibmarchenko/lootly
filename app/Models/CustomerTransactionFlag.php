<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTransactionFlag extends Model
{
    protected $fillable = [
        'customer_id',
        'locked',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
