<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Laravel\Spark\Services\Currency\CurrenciesRatesManager;

class RefreshRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh db stored exchange rates';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $currencyExchangeRateService = new CurrenciesRatesManager();

        /** @var Currency $currencyWeBuy */
        foreach (Currency::all() as $currencyWeBuy) {
            /** @var Currency $currencyWeSell */
            foreach (Currency::all() as $currencyWeSell) {

                $rate = $currencyExchangeRateService->getRateFromApi($currencyWeBuy, $currencyWeSell);

                if ($rate && $rate != 1.00) {
                    //adjust rate to make sure we dont loose on conversions
                    $rate = $rate - $rate / 100;
                }

                if ($rate)
                    $currencyExchangeRateService->setRate($currencyWeBuy, $currencyWeSell, $rate);
            }
        }
        \Log::info('Currency Refreshed');

    }

}
