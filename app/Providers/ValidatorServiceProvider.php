<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider{

    public function boot()
    {
        $this->app['validator']->extend('base64image', function ($attribute, $value, $parameters)
        {
            $explode = explode(',', $value);
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );

            // check file format
            if (!in_array($format, $parameters)) {
                return false;
            }

            // check base64 format
            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }

            return true;
        });

        $this->app['validator']->extend('base64size', function ($attribute, $value, $parameters)
        {

            $explode = explode(',', $value);
            $format = str_replace(
                [
                    'data:image/',
                    ';',
                    'base64',
                ],
                [
                    '', '', '',
                ],
                $explode[0]
            );


            $length = strlen(rtrim($explode[1], '='));
            $size = 3*ceil($length/4);

            // check file size
            if (!count($parameters) || $size/1024 > $parameters[0]) {
                return false;
            }

            // check base64 format
            if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
                return false;
            }
            return true;
        });
    }

    public function register()
    {
        //
    }

}