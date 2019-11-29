<?php

use Illuminate\Database\Seeder;

class BigcommerceIntegrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $integration = \App\Models\Integration::where( 'slug', 'bigcommerce' )->first();
        $integration->icon = '/images/icons/bigcommerce.png';
        $integration->logo = '/images/logos/bigcommerce.png';
        $integration->status = true;
        $integration->save();
    }
}
