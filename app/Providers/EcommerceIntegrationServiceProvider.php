<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class EcommerceIntegrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('shopify_ecommerce_integration', 'App\Helpers\EcommerceIntegration\ShopifyEcommerceIntegrationService');
        App::bind('magento_ecommerce_integration', 'App\Helpers\EcommerceIntegration\MagentoEcommerceIntegrationService');
        App::bind('woocommerce_ecommerce_integration', 'App\Helpers\EcommerceIntegration\WooCommerceEcommerceIntegrationService');
        App::bind('volusion_ecommerce_integration', 'App\Helpers\EcommerceIntegration\VolusionEcommerceIntegrationService');
        App::bind('bigcommerce_ecommerce_integration', 'App\Helpers\EcommerceIntegration\BigcommerceEcommerceIntegrationService');
        App::bind('custom_api_ecommerce_integration', 'App\Helpers\EcommerceIntegration\CustomApiEcommerceIntegrationService');
    }
}
