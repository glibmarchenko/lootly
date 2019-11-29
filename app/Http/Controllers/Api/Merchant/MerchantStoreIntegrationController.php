<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Facades\MerchantService;
use App\Facades\ShopifyApi;
use App\Http\Controllers\Controller;
use App\Merchant;
use Illuminate\Http\Request;

class MerchantStoreIntegrationController extends Controller
{
    protected $storeIntegration;

    public function __construct()
    {
        $this->storeIntegration = null;
    }

    public function getProducts(Request $request, Merchant $merchant)
    {
        // Get Store Integration
        $this->storeIntegration = app('merchant_service')->getStoreIntegration($merchant->id);

        if (! isset($this->storeIntegration)) {
            throw new \Exception('Cannot get e-commerce integration data');
        }

        $products = [];

        switch ($this->storeIntegration->slug) {
            case 'shopify':
                $products = app('shopify_ecommerce_integration')->getProducts($this->storeIntegration, $request->all());
                break;
            case 'magento':
                $products = app('magento_ecommerce_integration')->getProducts($this->storeIntegration, $request->all());
                break;
            case 'woocommerce':
                $products = app('woocommerce_ecommerce_integration')->getProducts($this->storeIntegration, $request->all());
                break;
            case 'volusion':
                $products = app('volusion_ecommerce_integration')->getProducts($this->storeIntegration, $request->all());
                break;
            case 'bigcommerce':
                $products = app('bigcommerce_ecommerce_integration')->getProducts($this->storeIntegration, $request->all());
                break;
            // ...
        }

        return response()->json(['data' => $products], 200);
    }
}