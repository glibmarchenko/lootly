<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ShopifyApi extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'shopify_api';
    }
}