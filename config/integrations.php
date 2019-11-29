<?php

return [

    'available_integrations' => [
        'shopify',
        'trustspot',
        'magento',
        'bigcommerce',
        'woocommerce',
        'volusion',
        'zapier',
        'common',
        'custom-api',        
        // ...
    ],

    'store_integrations' => [
        'shopify',
        'magento',
        'bigcommerce',
        'woocommerce',
        'volusion',
        'custom-api',
        // ...
    ],

    'common' => [
        'webhooks'                 => [// ...
        ],
        'webhook_middleware_class' => '\\App\\Http\\Middleware\\Helpers\\CommonWebhookVerifier',
    ],

    'shopify' => [

        'api_key' => env('SHOPIFY_APP_KEY', ''),

        'api_secret' => env('SHOPIFY_APP_SECRET', ''),

        'myshopify_domain' => env('SHOPIFY_MYSHOPIFY_DOMAIN', 'myshopify.com'),

        'api_scopes' => env('SHOPIFY_API_SCOPES', 'read_orders,read_customers,read_script_tags,write_script_tags,read_themes,write_themes,write_price_rules,read_products,write_customers'),

        'api_redirect' => env('SHOPIFY_API_REDIRECT', '/app/shopify/callback'),

        'webhooks' => [
            /*
            [
                'topic'   => env('SHOPIFY_WEBHOOK_1_TOPIC', 'orders/create'),
                'address' => env('SHOPIFY_WEBHOOK_1_ADDRESS', 'https://some-app.com/webhook/orders-create'),
            ],
            */
            [
                'topic'   => 'customers/create',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/customers-create',
                'format'  => 'json',
            ],
            [
                'topic'   => 'customers/delete',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/customers-delete',
                'format'  => 'json',
            ],
            [
                'topic'   => 'orders/create',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/orders-create',
                'format'  => 'json',
            ],
            [
                'topic'   => 'orders/fulfilled',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/orders-fulfilled',
                'format'  => 'json',
            ],
            [
                'topic'   => 'orders/paid',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/orders-paid',
                'format'  => 'json',
            ],
            [
                'topic'   => 'orders/cancelled',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/orders-cancelled',
                'format'  => 'json',
            ],
            [
                'topic'   => 'refunds/create',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/refunds-create',
                'format'  => 'json',
            ],
            [
                'topic'   => 'app/uninstalled',
                'address' => env('APP_URL').'/integrations/webhooks/shopify/app-uninstalled',
                'format'  => 'json',
            ],
        ],

        'webhook_middleware_class' => '\\App\\Http\\Middleware\\Helpers\\ShopifyWebhookVerifier',

        'scripttags' => [
            /*
            [
               'src' => env('SHOPIFY_SCRIPTTAG_1_SRC', 'https://some-app.com/some-controller/js-method-response'), // Required
               'event' => env('SHOPIFY_SCRIPTTAG_1_EVENT', 'onload'), // Required
               'display_scope' => env('SHOPIFY_SCRIPTTAG_1_DISPLAY_SCOPE', 'online_store') // Optional. Valid values: all, online_store or order_status
            ],
            ...
            */
            [
                'src'   => env('APP_URL').'/js/integrations/shopify/script.js',
                'event' => 'onload',
            ],
        ],

        'metafields' => [/*
            [
                'namespace' => env('SHOPIFY_METAFIELD_1_NAMESPACE', 'lootly'), // Required
                'key' => env('SHOPIFY_METAFIELD_1_KEY', 'api_key'), // Required
                'value' => env('SHOPIFY_METAFIELD_1_VALUE', 'APIKEY'), // Required
                'value_type' => env('SHOPIFY_METAFIELD_1_VALUE_TYPE', 'string') // Required. Valid values: string or integer
            ]
            ...
            */
        ],

        'assets' => [
            /*
            [
                'data' => [
                    'key' => env('SHOPIFY_ASSET_1_KEY', 'snippets/lootly-launcher.liquid'),
                    'value' => env('SHOPIFY_ASSET_1_VALUE', ''),
                ],
                'afterInstallJob' => env('', '\\App\\Jobs\\Integrations\\Shopify\\LootlyLauncherAssetIncludeJob') \\ Optional
            ]
            */
            [
                'data'            => [
                    'key'   => 'snippets/lootly-launcher.liquid',
                    'value' => str_replace('%APP_URL%', env('APP_URL'), file_get_contents(storage_path('integrations/shopify/assets/snippets/lootly-launcher.liquid'))),
                ],
                'afterInstallJob' => '\\App\\Jobs\\Integrations\\Shopify\\LootlyLauncherAssetIncludeJob',
            ],

        ],

        'settings_fields' => [
            'order_settings' => [
                'reward_status'     => 'str',
                'subtract_status'   => 'str',
                'include_taxes'     => 'bool',
                'subtotal'          => 'bool',
                'include_shipping'  => 'bool',
                'exclude_discounts' => 'bool',
                'include_previous_orders' => 'bool',
            ],
        ],

        'validator_rules' => [
            'order_settings.reward_status'    => 'required',
            'order_settings.subtract_status' => 'required',
        ],

    ],

    'bigcommerce' => [
        'webhooks' => [
            [
                'scope'   => 'store/customer/created',
                'destination' => env('APP_URL').'/integrations/webhooks/bigcommerce/customers-create',
                'is_active'  => true,
            ],
            [
                'scope'   => 'store/order/statusUpdated',
                'destination' => env('APP_URL').'/integrations/webhooks/bigcommerce/order-status-update',
                'is_active'  => true,
            ]
        ],
        'settings_fields' => [
            'order_settings' => [
                'reward_status'   => 'str',
                'subtract_status'   => 'str',
                'include_taxes'     => 'bool',
                'subtotal'          => 'bool',
                'include_shipping'  => 'bool',
                'exclude_discounts' => 'bool',
                'include_previous_orders' => 'bool'
            ],
        ],
    ],

    'woocommerce' => [
        'webhook_middleware_class' => '\\App\\Http\\Middleware\\Helpers\\CommonWebhookVerifier',

        'settings_fields' => [
            'order_settings' => [
                'reward_status'     => 'str',
                'subtract_status'   => 'str',
                'include_taxes'     => 'bool',
                'subtotal'          => 'bool',
                'include_shipping'  => 'bool',
                'exclude_discounts' => 'bool',
                'include_previous_orders' => 'bool',
            ],
        ],

        'validator_rules' => [
            'order_settings.reward_status'    => 'required',
            'order_settings.subtract_status' => 'required',
        ],

        'default_settings' => [
            'order_settings' => [
                'reward_status'           => 'processing',
                'subtract_status'         => 'refunded',
                'include_taxes'           => 0,
                'include_shipping'        => 0,
                'exclude_discounts'       => 1,
                'include_previous_orders' => 1,
            ],
        ],
    ],

    'magento' => [
        'webhook_middleware_class' => '\\App\\Http\\Middleware\\Helpers\\CommonWebhookVerifier',

        'settings_fields' => [
            'order_settings' => [
                'reward_status'     => 'str',
                'subtract_status'   => 'str',
                'include_taxes'     => 'bool',
                'subtotal'          => 'bool',
                'include_shipping'  => 'bool',
                'exclude_discounts' => 'bool',
                'include_previous_orders' => 'bool',
            ],
        ],

        'validator_rules' => [
            'order_settings.reward_status'    => 'required',
            'order_settings.subtract_status' => 'required',
        ],

        'default_settings' => [
            'order_settings' => [
                'reward_status'           => 'processing',
                'subtract_status'         => 'refunded',
                'include_taxes'           => 0,
                'include_shipping'        => 0,
                'exclude_discounts'       => 1,
                'include_previous_orders' => 1,
            ],
        ],
    ],

    'volusion' => [
        'webhook_middleware_class' => '\\App\\Http\\Middleware\\Helpers\\CommonWebhookVerifier',

        'settings_fields' => [
            'order_settings' => [
                'reward_status'     => 'str',
                'subtract_status'   => 'str',
                'include_taxes'     => 'bool',
                'subtotal'          => 'bool',
                'include_shipping'  => 'bool',
                'exclude_discounts' => 'bool',
                'include_previous_orders' => 'bool',
            ],
        ],

        'validator_rules' => [
            'order_settings.reward_status'    => 'required',
            'order_settings.subtract_status' => 'required',
        ],

        'default_settings' => [
            'order_settings' => [
                'reward_status'           => 'processing',
                'subtract_status'         => 'refunded',
                'include_taxes'           => 0,
                'include_shipping'        => 0,
                'exclude_discounts'       => 1,
                'include_previous_orders' => 1,
            ],
        ],
    ],

    'trustspot' => [

        'api_key' => env('TRUSTSPOT_APP_KEY', ''),

        'api_secret' => env('TRUSTSPOT_APP_SECRET', ''),

        'api_scopes' => env('TRUSTSPOT_API_SCOPES', ''),

        'api_redirect' => env('TRUSTSPOT_API_REDIRECT', '/app/trustspot/callback'),

        'webhooks' => [
            /* Not using yet - webhook installer is not used for trustspot */
            [
                'topic'   => 'product-review/create',
                'address' => env('APP_URL').'/integrations/webhooks/trustspot/product-review-create',
                'format'  => 'json',
            ],
        ],

        'webhook_middleware_class' => '\\App\\Http\\Middleware\\Helpers\\TrustSpotWebhookVerifier',

        //'settings_field_name' => 'credentials',

        // types: str, int, bool
        'settings_fields'          => [
            'credentials' => [
                'api_key'    => 'str',
                'secret_key' => 'str',
            ],
        ],

        'validator_rules' => [
            'credentials.api_key'    => 'required',
            'credentials.secret_key' => 'required',
        ],

    ],

    'zapier' => [

    ]

];
