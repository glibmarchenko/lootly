<?php

namespace App\Http\Controllers\Settings\Shopify;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Merchant;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PointRepository;
use App\Repositories\UserRepository;
use App\Services\Shopify\ConnectShopify;


class ConnectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ConnectShopify $connectShopify, UserRepository $userRepositoryContract, PointRepository $pointRepositoryContract,
                                OrderRepository $orderRepositoryContract, CustomerRepository $customerRepositoryContract)
    {
        $this->shopify = $connectShopify;
        $this->userRepository = $userRepositoryContract;
        $this->pointRepository = $pointRepositoryContract;
        $this->ordeRepository = $orderRepositoryContract;
        $this->customerRepository = $customerRepositoryContract;
        $this->middleware('auth');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function connect(Request $request)
    {
        $shopify = new ConnectShopify();

        $sh = $shopify->getConnectShopify($domain = null);
        $url = $sh->installURL(['permissions' => array('read_orders', 'read_customers'), 'redirect' => env('APP_URL') . '/oauthCallback']);

        return response()->json([
            'auth_url' => $url
        ]);
    }


    /**
     * Store webhooks event (create/order, paid/order, create/customer)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeWebhook(Request $request)
    {

        $store_data = $request->all();

        // Create Order
        $webhook_create_order = ['webhook' => ['topic' => 'orders/create', 'address' => env('APP_URL') . '/getShopifyEvent', 'format' => 'json']];
        $url = 'admin/webhooks.json';
        $response_order = $this->shopify->createWebhookEvent($webhook_create_order, $url, $store_data);

        // Order Spend
        $webhook_spend_order = ['webhook' => ['topic' => 'orders/paid', 'address' => env('APP_URL') . '/getShopifyEvent', 'format' => 'json']];
        $url = 'admin/webhooks.json';
        $response_paid = $this->shopify->createWebhookEvent($webhook_spend_order, $url, $store_data);

        // Create account
        $webhook_create_account = ['webhook' => ['topic' => 'customers/create', 'address' => env('APP_URL') . '/getShopifyEvent', 'format' => 'json']];
        $url = 'admin/webhooks.json';
        $response_create_acsount = $this->shopify->createWebhookEvent($webhook_create_account, $url, $store_data);

        // App Uninstalled
        $webhook_app_uninstalled = ['webhook' => ['topic' => ' app/uninstalled', 'address' => env('APP_URL') . '/getAppUninstalledEvent', 'format' => 'json']];
        $url = 'admin/webhooks.json';
        $response_app_uninstalled = $this->shopify->createWebhookEvent($webhook_app_uninstalled, $url, $store_data);

        return response()->json([
            'response_order' => $response_order,
            'response_paid' => $response_paid,
            'response_create_acsount' => $response_create_acsount,
            'response_app_uninstalled' => $response_app_uninstalled
        ]);
    }

    /**
     * Get webhook event (create/order, paid/order, create/customer)
     * @param Request $request
     */
    public function getWebhookEvent(Request $request)
    {

        $order_data = json_decode($request->getContent());
        $customer = $order_data->customer;
        $price = $order_data->total_price;
        $point = ($price * 10) / 100;


        $user = $this->customerRepository->hasCustomer($customer->email);

        if (!$user) {
            $user = $this->customerRepository->create($order_data);
        }

        $this->ordeRepository->create($user, $order_data);

        $this->pointRepository->create($order_data, $point);

    }

    public function getWebhookAppUninstallEvent(Request $request)
    {
        $merchant_data = json_decode($request->getContent());
        Merchant::query()->where('merchant_id', '=', $merchant_data->id)
            ->update([
                'shopify_installed' => '0'
            ]);
        return response()->json([
            'massages' => 'Success uninstall app'
        ]);
    }
}
