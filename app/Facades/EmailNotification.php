<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class EmailNotification extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'email_notification';
    }
}