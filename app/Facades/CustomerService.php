<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CustomerService extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'customer_service';
    }
}