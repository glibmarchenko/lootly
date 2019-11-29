<?php

namespace App\Http\Controllers\Webhook;

use App\Mail\MerchantPlanUpgrade;
use App\Mail\MerchantSubscriptionCancelled;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\BillingRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Subscription as StripeSubscription;
use Stripe\Customer as StripeCustomer;
use App\Models\Subscription;
use App\Models\Payment;

class StripeWebhookController extends WebhookController
{
    protected $payments;

    protected $plans;

    protected $subscriptions;

    protected $merchants;

    protected $users;

    protected $billings;

    public function __construct(
        PaymentRepository $payments,
        PlanRepository $plans,
        SubscriptionRepository $subscriptions,
        MerchantRepository $merchants,
        UserRepository $users,
        BillingRepository $billings
    ) {
        $this->payments = $payments;
        $this->plans = $plans;
        $this->subscriptions = $subscriptions;
        $this->merchants = $merchants;
        $this->users = $users;
        $this->billings = $billings;
    }

    public function handleWebhook(Request $request)
    {
        return parent::handleWebhook($request);
    }

    /**
     * Handle a Stripe webhook.
     *
     * @param  array  $payload
     * @return Response
     */
    public function handleInvoiceCreated($payload)
    {
        // Handle The Event
        // NOTE: When the trial ends, a new billing cycle starts for the customer.
        // Once the trial period is up, Stripe generates an invoice and sends an invoice.created event notification.
        // Approximately an hour later, Stripe attempts to charge that invoice.

        $customer = $payload['data']['object']['customer'] ?? null;
        $status = $payload['data']['object']['status'] ?? null;
        $paid = (bool) $payload['data']['object']['paid'] ?? false;
        $amount = (int) $payload['data']['object']['amount_paid'] ?? 0;
        $lines = $payload['data']['object']['lines']['data'] ?? [];

        if ($customer && $status === 'paid' && $paid === true && $amount && $lines) {
            foreach ($lines as $line) {
                $plan = $line['plan']['id'] ?? null;
                $subscription = $line['subscription'] ?? null;
                $periodEnd = $line['period']['end'] ?? time();

                if ($plan && $subscription) {
                    $this->subscriptions->clearEntity();

                    $merchantSubscription = $this->subscriptions->findWhereFirst([
                        'stripe_customer_id' => $customer,
                        'stripe_id' => $subscription,
                        'stripe_plan' => $plan,
                    ]);

                    if ($merchantSubscription && $merchantSubscription->id) {
                        $this->subscriptions->clearEntity();
                        $this->subscriptions->update($merchantSubscription->id, [
                            'status' => Subscription::STATUS_ACTIVE,
                            'ends_at' => Carbon::createFromTimestamp($periodEnd),
                            'updated_at' => Carbon::now(),
                        ]);

                        $this->payments->clearEntity();
                        $this->payments->create([
                            'merchant_id' => $merchantSubscription->merchant_id,
                            'user_id' => $merchantSubscription->user_id,
                            'service' => 'stripe',
                            'payment_id' => md5(time() . '_' . $merchantSubscription->merchant_id . '_' . $merchantSubscription->user_id),
                            'status' => Payment::STATUS_SUCCESS,
                            'price' => floatval($amount / 100),
                            'plan_id' => $merchantSubscription->plan_id,
                            'type' => $merchantSubscription->length == 365 ? 'yearly' : 'monthly',
                        ]);

                        $this->billings->create([
                            'user_id' => $merchantSubscription->user_id,
                            'merchant_id' => $merchantSubscription->merchant_id,
                            'plan_id' => $merchantSubscription->plan_id,
                            'name' => $merchantSubscription->plan->name ?? 'Unknown',
                            'price' => floatval($amount / 100),
                            'period' => $merchantSubscription->length == 365 ? 'Yearly' : 'Monthly',
                            'date' => Carbon::now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle a Stripe webhook.
     *
     * @param  array  $payload
     * @return Response
     */
    public function handleCustomerSubscriptionTrialWillEnd($payload)
    {
        // Handle The Event
        // NOTE: Three days before the trial period is up,
        // a customer.subscription.trial_will_end event is sent to your webhook endpoint.
        // You can use that notification as a trigger to take any necessary actions,
        // such as emailing your customer that billing for the plan is about to begin.

        $customer = $payload['data']['object']['customer'] ?? null;
        $status = $payload['data']['object']['status'] ?? null;
        $items = $payload['data']['object']['items']['data'] ?? [];

        if ($customer && $status && $items) {
            foreach ($items as $item) {
                $plan = $item['plan']['id'] ?? null;
                $subscription = $item['subscription'] ?? null;

                if ($plan && $subscription) {
                    $this->subscriptions->clearEntity();

                    $merchantSubscription = $this->subscriptions->findWhereFirst([
                        'stripe_customer_id' => $customer,
                        'stripe_id' => $subscription,
                        'stripe_plan' => $plan,
                    ]);

                    if ($merchantSubscription && $merchantSubscription->id && $merchantSubscription->status === 'trialing') {
                        $this->subscriptions->clearEntity();
                        $this->subscriptions->update($merchantSubscription->id, [
                            'status' => $status,
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Handle a successful checkout session from a Stripe subscription.
     *
     * @param  array $payload
     *
     * @return Response
     */
    protected function handleCheckoutSessionCompleted(array $payload)
    {
        // Send Status 200 OK
        $this->respondOK();

        if (! isset($payload['data']['object']['client_reference_id'])) {
            return new Response('Webhook Handled', 200);
        }

        $payment = $this->getPaymentByPaymentId($payload['data']['object']['client_reference_id']);

        if (is_null($payment) || ! $payment) {
            Log::error('No payment record for Stripe webhook event #'.$payload['id']);

            return new Response('Webhook Handled', 200);
        }

        $stripeSubscriptionId = $payload['data']['object']['subscription'];

        $stripeSubscription = StripeSubscription::retrieve($stripeSubscriptionId, config('services.stripe.secret'));

        // Get customer info
        $stripeCustomerId = $stripeSubscription->customer;
        $stripeCustomer = StripeCustomer::retrieve($stripeCustomerId, config('services.stripe.secret'));

        $stripeCustomerDefaultSourceId = $stripeCustomer->default_source;
        $stripeCustomerSources = $stripeCustomer->sources->data;

        // Get payment source info
        if (! isset($stripeSubscription->default_source) || ! $stripeSubscription->default_source) {
            $subscriptionSourceId = $stripeCustomerDefaultSourceId;
        } else {
            $subscriptionSourceId = $stripeSubscription->default_source;
        }
        $subscriptionSource = null;
        for ($i = 0; $i < count($stripeCustomerSources); $i++) {
            if ($stripeCustomerSources[$i]->id === $subscriptionSourceId) {
                $subscriptionSource = $stripeCustomerSources[$i];
                break;
            }
        }

        $period = 30;
        if ($payment->type == 'yearly') {
            $period = 365;
        }

        try {
            $plan = $this->plans->find($payment->plan_id);
        } catch (\Exception $exception) {
            Log::error('Account Upgrading Error: (Merchant #'.$payment->merchant_id.'; Payment #'.$payment->id.') '.$exception->getMessage());
        }
        if (! isset($plan) || ! $plan) {
            // Do something ...
            return new Response('Webhook Handled', 200);
        }
        $this->payments->update($payment->id, [
            'status' => 'success',
        ]);

        // Create/Update Subscription
        $subscriptionData = [
            'user_id'            => $payment->user_id,
            'stripe_product_id'  => '',
            'stripe_customer_id' => $stripeCustomer->id,
            'merchant_id'        => $payment->merchant_id,
            'plan_id'            => $plan->id,
            'name'               => isset($plan) && $plan ? $plan->name : '',
            'stripe_id'          => $stripeSubscription->id,
            'shopify_id'         => '',
            'stripe_plan'        => $stripeSubscription->plan->id,
            'quantity'           => $stripeSubscription->quantity,
            'length'             => $period,
            'status'             => 'active',
        ];

        if (isset($stripeSubscription->trial_end)) {
            $subscriptionData['trial_ends_at'] = Carbon::createFromTimestamp($stripeSubscription->trial_end);
            $subscriptionData['ends_at'] = $stripeSubscription->billing_cycle_anchor ? Carbon::createFromTimestamp($stripeSubscription->billing_cycle_anchor)
                ->addDays($period) : null;
        } else {
            $subscriptionData['ends_at'] = $stripeSubscription->billing_cycle_anchor ? Carbon::createFromTimestamp($stripeSubscription->billing_cycle_anchor)
                ->addDays($period) : null;
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

        $card_brand = '';
        $card_last_four = '';
        $card_country = '';

        if ($subscriptionSource->type == 'card') {
            $card_brand = $subscriptionSource->card->brand;
            $card_last_four = $subscriptionSource->card->last4;
            $card_country = $subscriptionSource->card->country;
        }

        try {
            $this->merchants->update($payment->merchant_id, [
                'payment_provider' => 'stripe',
                'stripe_id'        => $stripeCustomer->id,
                'card_brand'       => $card_brand,
                'card_last_four'   => $card_last_four,
                'card_country'     => $card_country,
            ]);
        } catch (\Exception $exception) {
            Log::error('Merchant #'.$payment->merchant_id.' payment provider updating error: '.$exception->getMessage());
        }

        return new Response('Webhook Handled', 200);
    }

    /**
     * Handle a cancelled customer from a Stripe subscription.
     *
     * @param  array $payload
     *
     * @return Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        // Send Status 200 OK
        $this->respondOK();

        try {
            $merchant = $this->merchants->findWhereFirst([
                'stripe_id' => $payload['data']['object']['customer'],
            ]);

            $merchantSubscription = $this->subscriptions->findWhereFirst([
                'merchant_id' => $merchant->id,
            ]);
            if ($merchantSubscription) {
                $this->subscriptions->clearEntity();
                try {
                    $this->subscriptions->update($merchantSubscription->id, [
                        'status' => 'cancelled',
                    ]);

                    if ($merchantSubscription->status != 'cancelled') {
                        try {
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
                    Log::error('Can\'t cancel subscription #'.$merchantSubscription->id.' for merchant #'.$merchant->id.'. '.$exception->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return new Response('Webhook Handled', 200);
    }

    protected function getPaymentByPaymentId($paymentId)
    {
        try {
            return $this->payments->findWhereFirst([
                'payment_id' => $paymentId,
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function respondOK($text = null)
    {
        // check if fastcgi_finish_request is callable
        if (is_callable('fastcgi_finish_request')) {
            if ($text !== null) {
                echo $text;
            }
            /*
             * http://stackoverflow.com/a/38918192
             * This works in Nginx but the next approach not
             */
            session_write_close();
            fastcgi_finish_request();

            return;
        }

        ignore_user_abort(true);

        ob_start();

        if ($text !== null) {
            echo $text;
        }

        $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        header($serverProtocol.' 200 OK');
        // Disable compression (in case content length is compressed).
        header('Content-Encoding: none');
        header('Content-Length: '.ob_get_length());

        // Close the connection.
        header('Connection: close');

        ob_end_flush();
        ob_flush();
        flush();
    }
}
