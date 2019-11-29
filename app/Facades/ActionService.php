<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ActionService extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'action_service';
    }
}