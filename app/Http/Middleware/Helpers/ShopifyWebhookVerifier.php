<?php

namespace App\Http\Middleware\Helpers;

class ShopifyWebhookVerifier
{
    static public function verify($request, $next)
    {
        $hmac = $request->header('x-shopify-hmac-sha256') ?: '';
        $shop = $request->header('x-shopify-shop-domain');
        $data = $request->getContent();
        // From https://help.shopify.com/api/getting-started/webhooks#verify-webhook
        $hmacLocal = base64_encode(hash_hmac('sha256', $data, config('integrations.shopify.api_secret'), true));
        if (!hash_equals($hmac, $hmacLocal) || empty($shop)) {
            // Issue with HMAC or missing shop header
            abort(401, 'Invalid webhook signature');
        }

        // All good, process webhook
        return $next($request);
    }
}