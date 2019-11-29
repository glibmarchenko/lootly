<?php

namespace App\Http\Controllers\Settings\Subscription;

use App\Http\Controllers\Controller;
use App\Repositories\MerchantRepository;
use App\Repositories\SubscriptionRepository;

class ShowController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->middleware('auth');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function show()
    {
        $merchant = new MerchantRepository();
        $merchantObj = $merchant->getCurrent();
        $invoiceObj = $this->subscriptionRepository->get($merchantObj);
        return $invoiceObj;
    }
}
