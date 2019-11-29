<?php

namespace App\Helpers;

use Postmark\PostmarkClient;

class PostmarkApi
{
    public function setup()
    {
        $api = new PostmarkClient(env('POSTMARK_TOKEN'));

        return $api;
    }
}