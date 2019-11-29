<?php

namespace App\Http\Controllers\Settings\Payment;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\MerchantRepository;
use App\Repositories\SubscriptionRepository;

use App\Services\Shopify\ConnectShopify;


class ShopifyController extends AbstractPaymentController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ConnectShopify $connectShopify, SubscriptionRepository $subscriptionRepositoryContract,
                                MerchantRepository $merchantRepository, SubscriptionRepository $subscriptionRepository)
    {
        $this->shopify = $connectShopify;
        $this->subscriptionRepository = $subscriptionRepositoryContract;
        $this->merchantRepository = $merchantRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->middleware('auth');
    }


    /**
     * Store webhooks event (create/order, paid/order, create/customer)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createSubscription(Request $request)
    {

        $merchant_id = $request->input('merchant');
        $plan_id = $request->input('plan_id');

        $merchantObj = $this->merchantRepository->find($merchant_id);
        $planObj = $this->subscriptionRepository->find($plan_id);

        $subscription = $this->shopify->createSubscription($merchantObj, $planObj);

        $subscriptionObj = $subscription->recurring_application_charge;
        $this->shopify->activateSubscription($subscriptionObj);

        $this->subscriptionRepository->createShopifySubscription($merchantObj, $subscription);

        return new Response('Create subscription', 200);
    }

    /**
     * @param $subscription_id
     * @return Response
     */
    public function deleteSubscription($subscription_id)
    {
        $this->shopify->deleteSubscription($subscription_id);

        $this->subscriptionRepository->delete($subscription_id);
        return new Response('Canseled subscription', 200);
    }


    public function failerCharge(Request $request)
    {

    }

    public function successfullCharge(Request $request)
    {

    }

    public function createInvoices(Request $request)
    {

    }

}
