<?php

namespace App\Http\Controllers\Settings\Payment;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\InvoicesRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\SubscriptionRepository;
use App\Services\Stripe;


class StripeController extends AbstractPaymentController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Stripe $stripe, SubscriptionRepository $subscriptionRepositoryContract,
                                InvoicesRepository $invoicesRepository, MerchantRepository $merchantRepository)
    {
        $this->stripe = $stripe;
        $this->subscriptionRepository = $subscriptionRepositoryContract;
        $this->merchantRepository = $merchantRepository;
        $this->invoicesRepository = $invoicesRepository;
//        $this->middleware('auth');
    }


    /**
     * @param Request $request
     */
    public function createSubscription(Request $request)
    {

        $plan_id = $request->input('plane');
        $merchant_id = $request->input('merchant');
        $stripe_token = $request->input('token');
        $planObj = $this->subscriptionRepository->find($plan_id);
        $merchantObj = $this->merchantRepository->find($merchant_id);


        $subscriptionObj = $this->stripe->createSuscription($planObj, $stripe_token);

        $this->subscriptionRepository->createStripeSubscription($subscriptionObj, $merchantObj);
    }

    /**
     * @param $subscription_id
     * @return Response
     */
    public function deleteSubscription($subscription_id)
    {
        $this->stripe->deleteSubscription($subscription_id);
        $this->subscriptionRepository->delete($subscription_id);

        return new Response('Canseled subscription', 200);

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function failerCharge(Request $request)
    {

        // send email
        return new Response('Webhook Charge failer', 200);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function successfullCharge(Request $request)
    {
        $subscription = json_decode($request->getContent());
        $subscriptionObj = $subscription->data->object;

        $invoice = $this->stripe->createInvoices($subscriptionObj);
        $this->invoicesRepository->createStripe($subscriptionObj);
        // Send email
        return new Response('Webhook Charge success', 200);
    }

    /**
     * @param Request $request
     */
    public function createInvoices(Request $request)
    {
//

    }
}
