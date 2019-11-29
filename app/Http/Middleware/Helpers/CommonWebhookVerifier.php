<?php

namespace App\Http\Middleware\Helpers;

use App\Repositories\Eloquent\EloquentMerchantDetailsRepository;
use Illuminate\Support\Facades\Log;

class CommonWebhookVerifier
{
    static public function verify($request, $next)
    {
        Log::info( 'CommonWebhookVerifier begin' );
        $hmac = $request->get('hmac') ?: '';
        $api_key = $request->get('key') ?: '';
        $data = $request->except([
            '_url',
            'hmac',
        ]);

        // Sorting data by keys
        ksort($data);

        /*
         * via getContent() way
         *
            $content = json_decode($request->getContent(), true);
            unset($content['hmac']);
            asort($content);
        */

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
                $hmacLocal = base64_encode(hash_hmac('sha256', json_encode($data), $merchantDetails[$i]->api_secret, true));
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
            $hmacLocal = base64_encode(hash_hmac('sha256', json_encode($data), $merchantDetails[0]->api_secret, true));
            if (! hash_equals($hmac, $hmacLocal)) { // || empty($shop)
                // Issue with HMAC
                abort(401, 'Invalid webhook signature');
            }

            $validMerchant = $merchantDetails[0];
        }

        $request->merge(['lootly_merchant_id' => $validMerchant->merchant_id]);

        Log::info( 'CommonWebhookVerifier end' );

        // All good, process webhook
        return $next($request);
    }
}
