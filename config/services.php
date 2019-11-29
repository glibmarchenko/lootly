<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'authy' => [
        'secret' => env('AUTHY_SECRET'),
    ],

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'plans'  => [
            'growth' => [
                'monthly' => env('STRIPE_PLAN_GROWTH_MONTHLY', 'plan_EXHrcEkXKSKu8W'),
                'yearly'  => env('STRIPE_PLAN_GROWTH_YEARLY', 'plan_EXHtY7TzXONfwq'),
            ],
            'ultimate' => [
                'monthly' => env('STRIPE_PLAN_ULTIMATE_MONTHLY', 'plan_EXHsArvQDOJBwL'),
                'yearly'  => env('STRIPE_PLAN_ULTIMATE_YEARLY', 'plan_EXHvXS70QmvJVj'),
            ],
            'enterprise' => [
                'monthly' => env('STRIPE_PLAN_ENTERPRISE_MONTHLY', 'plan_EXHsiGbXo4sHbY'),
                'yearly'  => env('STRIPE_PLAN_ENTERPRISE_YEARLY', 'plan_EXHv1wAj7eZ6aN'),
            ],
        ],
    ],

    'wkhtmltopdf' => [
        'bin' => '/usr/local/bin/wkhtmltopdf',
        'storage' => base_path('storage/bills/'), // require '/' in the end
        'image' => '/images/logos/logo-black.png', // related to path 'public'
        'template' => '/resources/views/account/PaymentReceipt.html' // related to root path
    ],
];
