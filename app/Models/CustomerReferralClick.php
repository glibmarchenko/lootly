<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerReferralClick extends Model
{
    protected $fillable = [
        'customer_id',
        'clicked_from'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
