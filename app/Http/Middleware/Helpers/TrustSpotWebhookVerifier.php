<?php

namespace App\Http\Middleware\Helpers;

use App\Repositories\Eloquent\EloquentMerchantDetailsRepository;

class TrustSpotWebhookVerifier
{
    static public function verify($request, $next)
    {
        $hmac = $request->get('hmac') ?: '';
        $api_key = $request->get('key') ?: '';
        $data = $request->get('data');

        $merchantDetailsRepository = new EloquentMerchantDetailsRepository();

        $merchantDetails = $merchantDetailsRepository->findWhere([
            'api_key' => trim($api_key),
        ]);

        if (! $merchantDetails || ! count($merchantDetails)) {
            abort(401, 'Invalid webhook data');
        }

        $validMerchant = null;

        if (count($merchantDetails) > 1) {
            for ($i = 0; $i < count($merchantDetails); $i++) {
                $hmacLocal = base64_encode(hash_hmac('sha256', $data['customer_email'], $merchantDetails[$i]->api_secret, true));
                if (hash_equals($hmac, $hmacLocal)) { // || empty($shop)
                    $validMerchant = $merchantDetails[$i];
                    break;
                }
            }

            if (! $validMerchant) {
                // Issue with HMAC
                abort(401, 'Invalid webhook signature');
            }
        } else {
            $hmacLocal = base64_encode(hash_hmac('sha256', $data['customer_email'], $merchantDetails[0]->api_secret, true));
            if (! hash_equals($hmac, $hmacLocal)) { // || empty($shop)
                // Issue with HMAC
                abort(401, 'Invalid webhook signature');
            }

            $validMerchant = $merchantDetails[0];
        }

        $request->merge(['lootly_merchant_id' => $validMerchant->merchant_id]);

        // All good, process webhook
        return $next($request);
    }
}