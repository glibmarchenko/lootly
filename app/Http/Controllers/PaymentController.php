<?php

namespace App\Http\Controllers;

use App\Mail\MerchantPlanUpgrade;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Contracts\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    protected $payments;

    protected $subscriptions;

    protected $plans;

    protected $merchants;

    protected $users;

    public function __construct(
        PaymentRepository $payments,
        SubscriptionRepository $subscriptions,
        PlanRepository $plans,
        MerchantRepository $merchants,
        UserRepository $users
    ) {
        $this->payments = $payments;
        $this->subscriptions = $subscriptions;
        $this->plans = $plans;
        $this->merchants = $merchants;
        $this->users = $users;
    }

    public function accept(Request $request, $service)
    {
        switch ($service) {
            case 'shopify':
                if (! $request->input('charge_id')) {
                    return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. No payment info found.']);
                }

                return $this->acceptShopifyCharge($request->input('charge_id'));
                break;
        }
    }

    public function success(Request $request, $service)
    {
        switch ($service) {
            case 'stripe':
                return redirect('/account/upgrade')->with('success', 'Your account was successfully upgraded.');
                break;
        }
    }

    public function cancel(Request $request, $service)
    {
        switch ($service) {
            case 'stripe':
                return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. No payment info found.']);
                break;
        }
    }

    private function acceptShopifyCharge($paymentId)
    {
        try {
            $payment = $this->payments->findWhereFirst([
                'service'    => 'shopify',
                'payment_id' => $paymentId,
            ]);
            $this->payments->clearEntity();
        } catch (\Exception $exception) {
            //
        }
        if (! isset($payment) || ! $payment) {
            return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. No payment info found.']);
        }

        $shopifyIntegration = app('merchant_service')->getStoreIntegration($payment->merchant_id);

        if (! $shopifyIntegration || $shopifyIntegration->slug != 'shopify') {
            return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. You need to setup Shopify integration first.']);
        }

        $period = 30;
        if ($payment->type == 'yearly') {
            $period = 365;
        }

        $api = app('shopify_api')->setup();
        $api->setShop($shopifyIntegration->pivot->external_id);
        $api->setAccessToken($shopifyIntegration->pivot->token);

        try {
            try {
                if ($payment->type != 'yearly') {
                    $activateChargeResponse = $api->rest('POST', '/admin/recurring_application_charges/'.$paymentId.'/activate.json', [])->body->recurring_application_charge;
                } else {
                    $activateChargeResponse = $api->rest('POST', '/admin/application_charges/'.$paymentId.'/activate.json', [])->body->application_charge;
                }
            } catch (\Exception $exception) {
                Log::error('Account Upgrading Error: (Merchant #'.$payment->merchant_id.'; Payment #'.$payment->id.')'.$exception->getMessage());

                return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. An error occurred on payment verification.']);
            }
            if (in_array(strtolower($activateChargeResponse->status), [
                'active',
            ])) {
                try {
                    $plan = $this->plans->find($payment->plan_id);
                } catch (\Exception $exception) {
                    Log::error('Account Upgrading Error: (Merchant #'.$payment->merchant_id.'; Payment #'.$payment->id.') '.$exception->getMessage());
                }
                if (! isset($plan) || ! $plan) {
                    // Cancel charge and show error
                    try {
                        $api->rest('DELETE', '/admin/recurring_application_charges/'.$paymentId.'.json');
                    } catch (\Exception $exception) {
                        Log::error('Account Upgrading Error: (Merchant #'.$payment->merchant_id.'; Payment #'.$payment->id.')'.$exception->getMessage());
                    }

                    return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. Selected plan isn\'t valid. Payment was canceled.']);
                }
                $this->payments->update($payment->id, [
                    'status' => strtolower($activateChargeResponse->status),
                ]);

                // Create/Update Subscription
                $subscriptionData = [
                    'user_id'            => $payment->user_id,
                    'stripe_product_id'  => '',
                    'stripe_customer_id' => '',
                    'merchant_id'        => $payment->merchant_id,
                    'plan_id'            => $plan->id,
                    'name'               => isset($plan) && $plan ? $plan->name : '',
                    'stripe_id'          => null,
                    'shopify_id'         => $paymentId,
                    'stripe_plan'        => null,
                    'quantity'           => null,
                    'length'             => $period,
                    'status'             => 'active',
                ];

                if (isset($activateChargeResponse->billing_on)) {
                    $subscriptionData['trial_ends_at'] = Carbon::createFromTimestamp(strtotime($activateChargeResponse->billing_on));
                    $subscriptionData['ends_at'] = Carbon::createFromTimestamp(strtotime($activateChargeResponse->billing_on))
                        ->addDays($period);
                } else {
                    $subscriptionData['ends_at'] = Carbon::createFromTimestamp(strtotime($activateChargeResponse->created_at))
                        ->addDays($period);
                }

                $currentPlanId = null;

                try {
                    $getCurrentSubscriptionIfExists = $this->subscriptions->findWhereFirst([
                        'merchant_id' => $payment->merchant_id,
                    ]);
                    $currentPlanId = $getCurrentSubscriptionIfExists->plan_id;
                } catch (\Exception $e) {
                    // No subscription yet
                }

                $this->subscriptions->clearEntity();

                $this->subscriptions->updateOrCreate([
                    'merchant_id' => $payment->merchant_id,
                ], $subscriptionData);

                $sendEmailNotification = false;

                if ($plan->id != $currentPlanId) {
                    if ($currentPlanId) {
                        $this->plans->clearEntity();
                        try {
                            $currentPlan = $this->plans->find($currentPlanId);
                            if ($currentPlan->growth_order < $plan->growth_order) {
                                $sendEmailNotification = true;
                            }
                        } catch (\Exception $e) {
                            // No plan with such id
                            $sendEmailNotification = true;
                        }
                    } else {
                        $sendEmailNotification = true;
                    }
                }

                if ($sendEmailNotification) {
                    try {
                        $user = $this->users->find($payment->user_id);
                        Mail::to($user->email)->queue(new MerchantPlanUpgrade($user, $plan));
                    } catch (\Exception $e) {
                        Log::error('An error occurred while attempting to send email notification to user #'.$payment->user_id.' on merchant #'.$payment->merchant_id.' plan upgrade.'.$e->getMessage());
                    }
                }

                // Update Merchant's Plan
                try {
                    $this->merchants->update($payment->merchant_id, [
                        'payment_provider' => 'shopify',
                    ]);
                } catch (\Exception $exception) {
                    return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. Subscription plan cannot be updated.']);
                }

                return redirect('/account/upgrade')->with('success', 'Your account was successfully upgraded.');
            } else {
                Log::error('Account Upgrading Error: (Merchant #'.$payment->merchant_id.'; Payment #'.$payment->id.') '.$exception->getMessage());

                return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. An error occurred on payment verification.']);
            }
        } catch (\Exception $exception) {
            Log::error('Account Upgrading Error: (Merchant #'.$payment->merchant_id.'; Payment #'.$payment->id.') '.$exception->getMessage());

            return redirect('account/upgrade')->withErrors(['error' => 'Account was not upgraded. An error occurred on payment verification.']);
        }
    }
}