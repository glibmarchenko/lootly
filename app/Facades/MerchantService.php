<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MerchantService extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'merchant_service';
    }
}