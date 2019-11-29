<?php

namespace App\Repositories;


use App\Models\MerchantDetail;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Repositories\MerchantDetailRepository as MerchantDetailContractRepository;

class MerchantDetailRepository implements MerchantDetailContractRepository
{
    public $baseQuery;

    public function __construct()
    {
        $this->baseQuery = MerchantDetail::query();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function findBy($key, $value)
    {
        return MerchantDetail::where($key, $value)->first();
    }

    public function create($merchantObj)
    {

        return $this->baseQuery->updateOrCreate(
            ['shop_domain' => $merchantObj->input('shop_domain'),
                'user_id' => Auth::user()->id,],
            [
                'shopify_api_key' => $merchantObj->input('api_key'),
                'shopify_api_secret' => $merchantObj->input('api_secret')
            ]);

    }

    public function update($shopifyAppObj, $merchantObj, $accessToken)
    {

        $hmac = $shopifyAppObj->input('hmac');
        $shop_domain = $shopifyAppObj->input('shop');

        return $this->baseQuery->updateOrCreate(
            ['merchant_id' => $merchantObj->id,
                'shop_domain' => $shop_domain],
            [
                'shopify_api_key' => env('SHOPIFY_APP_KEY'),
                'shopify_api_secret' => env('SHOPIFY_APP_SECRET'),
                'hmac' => $hmac,
                'token' => $accessToken
            ]);

    }

    public function delete($user)
    {
        // TODO: Implement delete() method.
    }

    public function getByShopDomain($shop_domain){
        return MerchantDetail::where('ecommerce_shop_domain', $shop_domain)->get();
    }
}
