<?php

namespace App\Services\Currency;


use App\Models\Currency;
use App\Models\CurrencyExchangeRate;
use Illuminate\Database\Eloquent\Collection;

class CurrenciesRatesManager
{
    /**
     * @param Currency $currencyWeBuy
     * @param Currency $currencyWeSell
     * @param $rate
     */
    public function setRate($currencyWeBuy, $currencyWeSell, $rate)
    {
        CurrencyExchangeRate::query()->updateOrCreate(
            ['we_buy_id' => $currencyWeBuy->id, 'we_sell_id' => $currencyWeSell->id],
            ['rate' => $rate]
        );
    }


    /**
     * @param Currency $currencyWeBuy
     * @param Currency $currencyWeSell
     * @return float
     */
    public function getRateFromApi($currencyWeBuy, $currencyWeSell)
    {
        $api = new ForgeApi();

        $rate = $api::getRate($currencyWeBuy, $currencyWeSell);

        if ($rate) return $rate;

        return 0;
    }


}