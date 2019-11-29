<?php

namespace App\Helpers;

use OhMyBrew\BasicShopifyAPI;

class ShopifyApi
{

    public function setup($api_key = null, $api_secret = null)
    {
        $api = new BasicShopifyAPI();
        $api->setApiKey($api_key ?? config('integrations.shopify.api_key'));
        $api->setApiSecret($api_secret ?? config('integrations.shopify.api_secret'));

        return $api;
    }

    public function sanitizeShopDomain($domain)
    {
        if (empty($domain)) {
            return;
        }

        $configEndDomain = config('integrations.shopify.myshopify_domain');
        $domain = preg_replace('/https?:\/\//i', '', trim($domain));

        if (strpos($domain, $configEndDomain) === false && strpos($domain, '.') === false) {
            // No myshopify.com ($configEndDomain) in shop's name
            $domain .= ".{$configEndDomain}";
        }

        // Return the host after cleaned up
        return parse_url("http://{$domain}", PHP_URL_HOST);
    }

}