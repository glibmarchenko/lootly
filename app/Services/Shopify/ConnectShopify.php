<?php

namespace App\Services\Shopify;


use App\Models\MerchantDetail;
use App\Models\Webhooks;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ConnectShopify
{
    /**
     * @param $store_domain
     * @return mixed
     */
    public function getConnectShopify($store_domain)
    {
//        $user_details = MerchantDetail::query()->where('shop_domain', $store_domain)->first();
//        $token = $user_details->token;
//        $shop_domain = $user_details->shop_domain;
//        $app_key = $user_details->shopify_api_key;
//        $app_secret = $user_details->shopify_api_secret;
        $sh = App::make('ShopifyAPI');
        $sh->setup(['API_KEY' => env('SHOPIFY_APP_KEY'), 'API_SECRET' => env('SHOPIFY_APP_SECRET'), 'SHOP_DOMAIN' => env('SHOPIFY_APP_URL')]);
        return $sh;
    }

    public function setupShopifyConnection($shop_domain){
        $sh = App::make('ShopifyAPI');
        $sh->setup(['API_KEY' => env('SHOPIFY_APP_KEY'), 'API_SECRET' => env('SHOPIFY_APP_SECRET'), 'SHOP_DOMAIN' => $shop_domain]);
        return $sh;
    }
//    public function getConnectShopify()
//    {
//        $api_key = env('SHOPIFY_APP_KEY');
//        $api_secret = env('SHOPIFY_APP_SECRET');
//        $api_domain = env('SHOPIFY_APP_URL');
//
//        $sh = App::make('ShopifyAPI', ['API_KEY' => $api_key, 'API_SECRET' => $api_secret, 'SHOP_DOMAIN' => $api_domain]);
//        return $sh;
//    }
    /**
     * @param $webhookData
     * @param $url
     * @param $method
     * @return \Illuminate\Http\JsonResponse
     */
    public function createWebhookEvent($webhookData, $url, $store_data)
    {

        $store_domain = $store_data['shop_domain'];
        $sh = $this->getConnectShopify($store_domain);
        $call = $sh->call([
            'URL' => $url,
            'METHOD' => 'POST',
            'DATA' => $webhookData,
        ]);

        $response = $call->webhook;
        Webhooks::query()->create([
            'user_id' => Auth::user()->id,
            'webhook_id' => $response->id,
            'topic' => $response->topic,
            'address' => $response->address
        ]);
        return response()->json([
            'response' => $response
        ]);
    }

    public function editWebhookEvent($webhookID, $url, $webhookData)
    {

    }

    public function deleteWebhookEvent($webhookID)
    {

    }

    public function createSubscription($merchantObj, $planObj)
    {
        $merchant_data = MerchantDetail::query()->where('user_id', '=', Auth::user()->id)->first();
        $shop_domain = $merchant_data->shop_domain;

        $sh = $this->getConnectShopify($shop_domain);

        $url = '/admin/recurring_application_charges.json';
        $data = ['recurring_application_charge' =>
            [
                'name' => $merchantObj->name,
                'price' => $planObj->price,
                "return_url" => env('APP_URL') . '/payment/charge/accept'

            ]

        ];
        $subscription = $sh->call([
            'URL' => $url,
            'METHOD' => 'POST',
            'DATA' => $data,
        ]);

        return $subscription;
    }

    public function activateSubscription($subscriptionObj)
    {

        $merchant_data = MerchantDetail::query()->where('user_id', '=', Auth::user()->id)->first();
        $shop_domain = $merchant_data->shop_domain;

        $sh = $this->getConnectShopify($shop_domain);
        $activate_url = '/admin/recurring_application_charges/' . $subscriptionObj->id . 'activate.json';
        $activate_call = $sh->call([
            'URL' => $activate_url,
            'METHOD' => 'POST',
        ]);
        return $activate_call;
    }

    public function getSubscription()
    {

    }

    public function deleteSubscription()
    {

    }
}
