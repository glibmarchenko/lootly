<?php

use Illuminate\Database\Seeder;

class IntegrationTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $integrations_arr = [
            // Shopify
            [
                'slug' => 'shopify',
                'title' => 'Shopify',
                'description' => 'Shopify is a leading cloud-based eCommerce platform utilized by more than 600k stores.',
                'logo' => '/images/logos/shopify.png',
                'icon' => '/images/icons/shopify.png',
                'status' => 1,
                'order' => 0,
            ],
            // TrustSpot
            [
                'slug' => 'trustspot',
                'title' => 'TrustSpot',
                'description' => 'Reward customers for writing a product or company review.',
                'logo' => '/images/logos/trustspot.png',
                'icon' => '/images/icons/trustspot.png',
                'status' => 1,
                'order' => 2,
            ],
            // Klaviyo
            [
                'slug' => 'klaviyo',
                'title' => 'Klaviyo',
                'description' => 'Email marketing service to personalize, and measure results for ecommerce',
                'logo' => '/images/logos/klaviyo.png',
                'icon' => '/images/logos/klaviyo.png',
                'status' => 0,
                'order' => 3,
            ],
            // BigCommerce
            [
                'slug' => 'bigcommerce',
                'title' => 'BigCommerce',
                'description' => 'BigCommerce is a feature rich and robust eCommerce platform for brands.',
                'logo' => '/images/logos/bigcommerce.png',
                'icon' => '/images/logos/bigcommerce.png',
                'status' => 0,
                'order' => 4,
            ],
            // Magento
            [
                'slug' => 'magento',
                'title' => 'Magento 1',
                'description' => 'Magento Commerce is one of the largest enterprise platforms for growing brands.',
                'logo' => '/images/logos/magento.png',
                'icon' => '/images/logos/magento.png',
                'status' => 0,
                'order' => 5,
            ],
            // WooCommerce
            [
                'slug' => 'woocommerce',
                'title' => 'WooCommerce',
                'description' => 'WooCommerce is an open-source platform helping stores expand their business.',
                'logo' => '/images/logos/woocommerce.png',
                'icon' => '/images/logos/woocommerce.png',
                'status' => 1,
                'order' => 1,
            ],
            // Magento 2
            [
                'slug' => 'magento-2',
                'title' => 'Magento 2',
                'description' => 'Magento 2 is the latest iteration of the Magento Commerce platform, allowing brands to scale faster and more efficiently.',
                'logo' => '/images/logos/magento.png',
                'icon' => '/images/logos/magento.png',
                'status' => 0,
                'order' => 6,
            ],
            // Volusion
            [
                'slug' => 'volusion',
                'title' => 'Volusion',
                'description' => 'Volusion integration',
                'logo' => '/images/logos/volusion.svg',
                'icon' => '/images/icons/volusion.svg',
                'status' => 0,
                'order' => 8,
            ],
            // Zapier
            [
                'slug' => 'zapier',
                'title' => 'Zapier',
                'description' => 'Zapier adds powerful automation to Lootly by connecting you to more than 1,500 web apps.',
                'logo' => '/images/logos/zapier.png',
                'icon' => '/images/logos/zapier.png',
                'status' => 0,
                'order' => 7,
            ],
            // Custom integration
            [
                'slug' => 'custom',
                'title' => 'Custom',
                'description' => '',
                'logo' => '',
                'icon' => '',
                'status' => 0,
                'order' => 8,
            ],
            // Custom API
            [
                'slug' => 'custom-api',
                'title' => 'Custom API',
                'description' => 'Select to enable or disable the Lootly API. By enabling the API all other eCommerce platform connections are disabled and manual code upload is now available.',
                'logo' => '/images/logos/api.svg',
                'icon' => '/images/logos/api.svg',
                'is_api' => 1,
                'status' => 1,
                'order' => 10,
            ],
        ];
        DB::table('integrations')->delete();
        DB::table('integrations')->insert($integrations_arr);
    }
}
