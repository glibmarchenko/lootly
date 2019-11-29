<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReferralShare extends Model
{
    protected $fillable = [
        'customer_id',
        'shared_to'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
