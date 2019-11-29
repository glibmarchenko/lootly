<?php

namespace App\Http\Controllers\Settings;


use Illuminate\Http\Request;
use App\Repositories\MerchantDetailRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\UserMerchantRepository;
use App\Services\Shopify\ConnectShopify;


class DashboardController extends \Laravel\Spark\Http\Controllers\Settings\DashboardController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ConnectShopify $connectShopify, MerchantRepository $merchantRepository,
                                MerchantDetailRepository $merchantDetailRepository, UserMerchantRepository $userMerchantRepository)
    {
        parent::__construct();
        $this->merchantRepository = $merchantRepository;
        $this->userMerchantRepository = $userMerchantRepository;
        $this->merchantDetailRepository = $merchantDetailRepository;
        $this->shopify = $connectShopify;
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function oauthCallback(Request $request)
    {

        $shopifyAppObj = $request;

        $code = $request->input('code');
        $shop_domain = $request->input('shop');
        $sh = $this->shopify->getConnectShopify($shop_domain);

//
//        $user_detals = MerchantDetail::query()->where('shop_domain', $shop_domain)->first();
//        $api_key = $user_detals->shopify_api_key;
//        $api_secret = $user_detals->shopify_api_secret;
//        $shop_domain = $user_detals->shop_domain;
        try {
            $accessToken = $sh->getAccessToken($code);

        } catch (\Exception $e) {
            echo '<pre>Error: ' . $e->getMessage() . '</pre>';
        }
        $merchantObj = $this->merchantRepository->getCurrent();
        $this->merchantDetailRepository->update($shopifyAppObj,$merchantObj, $accessToken);

//        $shop = \App::make('ShopifyAPI');
//        $shop->setup(['API_KEY' => $api_key, 'API_SECRET' => $api_secret, 'SHOP_DOMAIN' => $shop_domain, 'ACCESS_TOKEN' => $accessToken]);
//        $merchantObj = $shop->call([
//            'URL' => 'admin/shop.json',
//            'METHOD' => 'GET',
//        ]);
//        $userObj = Auth::user();
//        $this->merchantRepository->create($userObj, $merchantObj);
//        $stripe = new Stripe();
//
//        $stripe->createProducts($merchantObj->shop);

//        $merchantId = $this->merchantRepository->getId($merchantObj);


        return redirect('account/settings');
    }
}
