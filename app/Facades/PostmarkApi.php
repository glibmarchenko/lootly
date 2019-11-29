<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PostmarkApi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'postmark_api';
    }
}