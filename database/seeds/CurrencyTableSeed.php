<?php

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencyTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency_arr = [
            [
                'name' => 'USD',
                'currency_sign' => '$',
                'display_type' => 'United States Dollar',
            ],
            [
                'name' => 'CAD',
                'currency_sign' => '$',
                'display_type' => 'Canadian Dollar',
            ],
            [
                'name' => 'EUR',
                'currency_sign' => '€',
                'display_type' => 'Euro',
            ],
            [
                'name' => 'GBP',
                'currency_sign' => '£',
                'display_type' => 'British Pound',
            ],
            [
                'name' => 'AUD',
                'currency_sign' => '$',
                'display_type' => 'Australian Dollar',
            ],
            [
                'name' => 'NZD',
                'currency_sign' => '$',
                'display_type' => 'New Zealand Dollar',
            ],
            [
                'name' => 'DKK',
                'currency_sign' => 'Kr.',
                'display_type' => 'Danish Krone',
            ],
            [
                'name' => 'NOK',
                'currency_sign' => 'kr',
                'display_type' => 'Norwegian Krone',
            ],
            [
                'name' => 'CHF',
                'currency_sign' => 'Fr.',
                'display_type' => 'Swiss Franc',
            ],
            [
                'name' => 'MXN',
                'currency_sign' => '$',
                'display_type' => 'Mexican Peso',
            ],
            [
                'name' => 'INR',
                'currency_sign' => '₹',
                'display_type' => 'Indian Rupee',
            ],
        ];
        Currency::query()->delete();
        Currency::query()->insert($currency_arr);

    }
}
