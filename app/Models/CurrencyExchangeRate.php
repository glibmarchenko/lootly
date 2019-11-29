<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CurrencyExchangeRate extends Model
{


    protected $table = 'currency_exchange_rates';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'we_buy_id', 'we_sell_id', 'rate'
    ];

}
