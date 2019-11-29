<?php

namespace App\Services\Currency;


use App\Models\Currency;

class ForgeApi
{
    /**
     * @param Currency $from
     * @param Currency $to
     * @return float
     */
    public static function getRate($from, $to)
    {
        if ($from->id == $to->id) return 1;

        $rate = 0;


        try {
            $request = "https://forex.1forge.com/1.0.2/convert?from=" . $from->name . "&to=" . $to->name . "&quantity=1&api_key=" . env('FORGE_CURRENCIES_RATES_API_KEY');

            $result = file_get_contents($request);

            if ($result) {

                $resultArray = \GuzzleHttp\json_decode($result, true);

                $rate = isset($resultArray['value']) ? $resultArray['value'] : 0;

                if (isset($resultArray['message'])) {

                    \Log::error($resultArray['message']);
                }
            }
        } catch (\Exception $e) {

            \Log::error($e);
        }

        if ( ! $rate)
            \Log::warning('wasnt able to get exchange rate from API ' . self::class);

        return $rate;
    }
}