<?php

namespace App\Http\Controllers\Settings\Payment;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;
use App\Services\Stripe;

abstract class AbstractPaymentController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public abstract function failerCharge(Request $request);

    public abstract function successfullCharge(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public abstract function createSubscription(Request $request);

    /**
     * @param $subscription_id
     * @return mixed
     */
    public abstract function deleteSubscription($subscription_id);

    /**
     * @param Request $request
     * @return mixed
     */
    public abstract function createInvoices(Request $request);

    /**
     * @param Request $request
     * @return string
     */
    public function changeCreditCard(Request $request)
    {
        $cardDetail = $request->all();
        $stripe = new Stripe();
        $response = $stripe->changeCreditCard($cardDetail);
        return $response;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function getMerchants()
    {
        $merchant = new MerchantRepository();
        $merchantObj = $merchant->get();
        return $merchantObj;
    }
}