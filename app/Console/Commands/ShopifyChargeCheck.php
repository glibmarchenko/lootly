<?php

namespace App\Console\Commands;

use App\Mail\MerchantSubscriptionCancelled;
use App\Models\Billing;
use App\Models\MerchantDetail;
use App\Models\Subscription;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasActiveIntegrationWhere;
use App\Repositories\Eloquent\Criteria\WithActiveShopifyIntegrations;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Spark\Repositories\BillingRepository;
use Laravel\Spark\Repositories\InvoicesRepository;
use Laravel\Spark\Repositories\UserRepository;
use Laravel\Spark\Services\Shopify\ConnectShopify;

class ShopifyChargeCheck extends Command
{
    protected $merchants;

    protected $subscriptions;

    protected $payments;

    protected $users;

    protected $plans;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:check';

    public function __construct(
        MerchantRepository $merchants,
        SubscriptionRepository $subscriptions,
        PaymentRepository $payments,
        \App\Repositories\Contracts\UserRepository $users,
        PlanRepository $plans
    ) {
        parent::__construct();

        $this->merchants = $merchants;
        $this->subscriptions = $subscriptions;
        $this->payments = $payments;
        $this->users = $users;
        $this->plans = $plans;
    }

    public function handle()
    {
        // Check active subscriptions
        $this->checkSubscriptions();

        Log::info('Check Charge');
        //$this->checkCharge();
    }

    public function checkSubscriptions()
    {
        // Get merchants with Shopify payment provider
        $this->merchants->clearEntity();
        $merchants = $this->merchants->withCriteria([
            new EagerLoad([
                'plan_subscription',
            ]),
            new WithActiveShopifyIntegrations(),
        ])->findWhere([
            'payment_provider' => 'shopify',
        ]);

        /*$this->merchants->clearEntity();
        // Get all merchants with active Shopify integration
        $merchants = $this->merchants->withCriteria([
            new HasActiveIntegrationWhere([
                'slug' => 'shopify',
            ]),
            new EagerLoad([
                'plan_subscription',
            ]),
            new WithActiveShopifyIntegrations(),
        ])->all();*/

        $api = app('shopify_api')->setup();

        for ($i = 0; $i < count($merchants); $i++) {
            if (isset($merchants[$i]->integrationsWithToken) && count($merchants[$i]->integrationsWithToken)) {
                $shopDomain = $merchants[$i]->integrationsWithToken[0]->pivot->external_id;
                $accessToken = $merchants[$i]->integrationsWithToken[0]->pivot->token;

                if (trim($shopDomain) && trim($accessToken)) {
                    $api->setShop($shopDomain);
                    $api->setAccessToken($accessToken);
                } else {
                    // Deactivate Shopify integration
                    try {
                        app('merchant_service')->deactivateIntegration($merchants[$i], $merchants[$i]->integrationsWithToken[0]->id);
                    } catch (\Exception $e) {
                        Log::error('Error occurred while attempting to deactivate Shopify integration: '.$e->getMessage());
                    }
                    // Set Free Plan
                    $this->setFreePlan($merchants[$i]->id, true);
                    continue;
                }
            } else {
                // Set Free Plan
                $this->setFreePlan($merchants[$i]->id, true);
                continue;
            }
            // Get current Plan and Subscription
            if (isset($merchants[$i]->plan_subscription) && $merchants[$i]->plan_subscription) {

                if (in_array(strtolower($merchants[$i]->plan_subscription->status), [
                    'cancelled',
                ])) {
                    // Set Free Plan
                    $this->setFreePlan($merchants[$i]->id);
                    continue;
                }

                $days_length = strtolower($merchants[$i]->plan_subscription->length);
                $days = 30;
                switch ($days_length) {
                    case 30:
                    case 'month':
                        $days = 30;
                        break;
                    case 365:
                    case 'year':
                        $days = 365;
                }

                $charge_id = trim($merchants[$i]->plan_subscription->shopify_id);
                if (! $charge_id) {
                    // Set Free Plan
                    $this->setFreePlan($merchants[$i]->id);
                    continue;
                }

                if ($days == 365) {

                    if ($merchants[$i]->plan_subscription->ends_at->timestamp <= Carbon::now()->getTimestamp()) {
                        // Set Free Plan. Set subscription to frozen
                        $this->setFreePlan($merchants[$i]->id, false, 'frozen');
                    }

                    // Get Application Charge Info
                    try {
                        $charge = $api->rest('GET', '/admin/application_charges/'.$charge_id.'.json')->body->application_charge;

                        Log::info('Application Charge: '.print_r($charge, true));

                        if (! in_array($charge->status, [
                            'active',
                        ])) {
                            // Set Free Plan
                            $this->setFreePlan($merchants[$i]->id, false, 'frozen');
                            continue;
                        }
                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                    }
                } else {
                    // Get Recurring Charge Info
                    try {
                        $charge = $api->rest('GET', '/admin/recurring_application_charges/'.$charge_id.'.json')->body->recurring_application_charge;

                        Log::info(print_r('Recurring Charge: '.$charge, true));

                        if (! in_array($charge->status, [
                            'active',
                        ])) {
                            // Set Free Plan. Set subscription to frozen
                            $this->setFreePlan($merchants[$i]->id, false, 'frozen');
                            continue;
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());

                        // Set Free Plan
                        $this->setFreePlan($merchants[$i]->id, false, 'frozen');
                        continue;
                    }
                }
            } else {
                // Set Free Plan
                $this->setFreePlan($merchants[$i]->id);
                continue;
            }
        }
    }

    private function setFreePlan($merchantId, $unsetPaymentProvider = false, $subscriptionStatus = 'cancelled')
    {
        $this->merchants->clearEntity();
        try {
            $updateData = [];

            if ($unsetPaymentProvider !== false) {
                if (is_null($unsetPaymentProvider) || $unsetPaymentProvider === true) {
                    $updateData['payment_provider'] = null;
                } else {
                    if (in_array(trim($unsetPaymentProvider), [
                        'shopify',
                        'stripe',
                    ])) {
                        $updateData['payment_provider'] = trim($unsetPaymentProvider);
                    }
                }
            }

            if ($updateData) {
                $this->merchants->update($merchantId, $updateData);
            }
        } catch (\Exception $e) {
            Log::error('Can\'t downgrade plan for merchant #'.$merchantId.'. '.$e->getMessage());
        }

        // Cancel subscription if exists
        try {
            $merchantSubscription = $this->subscriptions->findWhereFirst([
                'merchant_id' => $merchantId,
            ]);
            if ($merchantSubscription) {
                $this->subscriptions->clearEntity();
                try {
                    $this->subscriptions->update($merchantSubscription->id, [
                        'status' => 'cancelled',
                    ]);

                    if ($merchantSubscription->status != 'cancelled') {
                        try {
                            $this->merchants->clearEntity();
                            $merchant = $this->merchants->find($merchantId);

                            $this->users->clearEntity();
                            $user = $this->users->find($merchant->owner_id);

                            $this->plans->clearEntity();
                            $plan = $this->plans->find($merchantSubscription->plan_id);

                            Mail::to($user->email)->queue(new MerchantSubscriptionCancelled($user, $plan));
                        } catch (\Exception $e) {
                            Log::error('An error occurred while attempting to send email notification to user #'.$user->id.' on merchant #'.$merchant->id.' plan cancel.'.$e->getMessage());
                        }
                    }
                } catch (\Exception $exception) {
                    Log::error('Can\'t cancel subscription #'.$merchantSubscription->id.' for merchant #'.$merchantId.'. '.$exception->getMessage());
                }
            }
        } catch (\Exception $e) {
            //
        }
    }

    public function checkCharge()
    {
        /*$shopify = new ConnectShopify();

        $merchants = MerchantDetail::query()->get();

        foreach ($merchants as $merchnat) {

            $sh = $shopify->getShopifyClient($merchnat->shop_domain);
            $subscriptions = $this->getSubscription($sh);

            foreach ($subscriptions as $subscription) {

                $charge_date = $subscription->billing_on;

                $this->addBilling($subscription);

                if ($charge_date == null || strtotime($charge_date) < strtotime('last Month')) {
                    $this->setfrozen($subscription, $sh);
                    Log::info('Subscription: '.$subscription->name.'  frozen');
                }
            }
        }*/
    }

    public function getSubscription($sh)
    {

        $url = '/admin/recurring_application_charges.json';
        $subscription = $sh->call([
            'URL'    => $url,
            'METHOD' => 'GET',
        ]);

        return $subscription->recurring_application_charges;
    }

    public function addBilling($subscription)
    {

        $subscriptionObj = Subscription::query()->where('name', '=', $subscription->name)->first();

        $user = new UserRepository();
        $userObj = $user->find($subscriptionObj->user_id);

        $billingObj = Billing::query()->where('name', '=', $subscription->name)->first();
        if ($billingObj->date != $subscription->billing_on) {
            $billing = new BillingRepository();
            $billing->add($userObj, $subscription);
            $this->addInvoices($subscriptionObj);
        }
    }

    public function addInvoices($subscriptionObj)
    {
        $invoice = new InvoicesRepository();
        $invoice->createShopify($subscriptionObj);
    }

    public function setfrozen($subscriptionObj, $sh)
    {

        $url = '/admin/recurring_application_charges/'.$subscriptionObj->id.'/customize.json?recurring_application_charge[price]=200';

        $call = $sh->call([
            'URL'    => $url,
            'METHOD' => 'PUT',
            'DATA'   => [
                'recurring_application_charge' => [
                    'status' => 'frozen',
                ],
            ],
        ]);
    }
}
