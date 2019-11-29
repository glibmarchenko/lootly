<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MerchantDetail extends Model
{


    protected $table = 'merchant_details';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id', 'ecommerce_shop_domain', 'api_key', 'api_secret'
    ];

    protected $hidden = [
        'api_key',
        'api_secret'
    ];

    public function merchant()
    {
        return $this->hasOne('App\Merchant', 'id','merchant_id');
    }

}
