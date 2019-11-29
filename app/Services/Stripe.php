<?php

namespace App\Services;

use App\User;
use App\Merchant;
use App\Models\SubscriptionPlan;
use App\Repositories\MerchantRepository;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Integer;
use Stripe\Card as StripeCard;
use Stripe\BankAccount as StripeBankAccount;

class Stripe extends \Laravel\Spark\Services\Stripe
{
    public function __construct()
    {
        //parent::__construct();
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    }

    public function createProducts($merchantObj)
    {
        $product = \Stripe\Product::create([
            'name' => $merchantObj->name,
            'type' => 'service',
        ]);

        return $product;
    }

    /**
     * @param SubscriptionPlan $planObj
     * @param $stripe_token
     * @return \Stripe\Subscription
     */
    public function createSuscription(SubscriptionPlan $planObj, $stripe_token)
    {
        $userObj = \App\User::getAuthClient();

        $customer = \Stripe\Customer::create(array(
            'email' => $userObj->email,
            'source' => $stripe_token,
        ));

        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [['plan' => $planObj->id]],
        ]);
        return $subscription;

    }

    public function createCustomerWithSource(string $source)
    {
        $user = User::getAuthClient();

        $customer = \Stripe\Customer::create(array(
            'email' => $user->email,
            'source' => $source,
        ));

        return $customer;
    }

    public function createSubscriptionTrial($customer, string $plan, int $days = 7)
    {
        $subscription = \Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [
                ['plan' => $plan]
            ],
            'trial_end' => Carbon::now()->addDays($days)->timestamp,
        ]);

        return $subscription;
    }

    public function getCustomerDefaultSource($customer)
    {
        // Next we will get the default source for this model so we can update the last
        // four digits and the card brand on the record in the database. This allows
        // us to display the information on the front-end when updating the cards.
        $source = $customer->default_source ? $customer->sources->retrieve($customer->default_source) : null;

        $cardBrand = null;
        $cardLastFour = null;
        $cardExpiration = null;
        $cardCountry = null;

        if ($source instanceof StripeCard) {
            $cardBrand = $source->brand;
            $cardLastFour = $source->last4;
            $cardCountry = $source->country;

            if (isset($source->exp_month) && isset($source->exp_year)) {
                $cardExpiration = sprintf("%02d", intval($source->exp_month)) . '/' . substr($source->exp_year, -2, 2);
            }

        } elseif ($source instanceof StripeBankAccount) {
            $cardBrand = 'Bank Account';
            $cardLastFour = $source->last4;
            $cardCountry = $source->country;

            if (isset($source->exp_month) && isset($source->exp_year)) {
                $cardExpiration = sprintf("%02d", intval($source->exp_month)) . '/' . substr($source->exp_year, -2, 2);
            }
        }

        return [
            'card_brand' => $cardBrand,
            'card_last_four' => $cardLastFour,
            'card_expiration' => $cardExpiration,
            'card_country' => $cardCountry,
        ];
    }

    public function cancelSubscriptionTrial(Subscription $subscription)
    {
        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->stripe_id);

        return $stripeSubscription->cancel();
    }

    public function deleteSubscription()
    {

    }

    public function createInvoices($subscriptionObj)
    {
        $userObj = $this->invoicesRepository->getByCusId($subscriptionObj);
        $invoice = \Stripe\Invoice::create([
            "customer" => $userObj->id,
        ]);

        return $invoice;
    }

    /**
     * @param $cardDetail
     * @return string
     */
    public function changeCreditCard($cardDetail)
    {
        $merchantObj = new MerchantRepository();
        $merchant = $merchantObj->getCurrent();
        try {
            $cu = \Stripe\Customer::retrieve($merchant->subscriptions[0]->stripe_customer_id);
            $cu->subscriptions->card = $cardDetail->number;
            $cu->save();

            return $success = "Your card details have been updated!";
        } catch (\Stripe\Error\Card $e) {

            $body = $e->getJsonBody();
            $err = $body['error'];
            return $error = $err['message'];
        }
    }

    public function getCheckoutRequestData(string $plan, string $paymentId): array
    {
        return [
            'checkout_request' => [
                'items' => [
                    [
                        'plan' => $plan,
                        'quantity' => 1,
                    ],
                ],
                'successUrl' => config('app.url') . '/payment/stripe/success',
                'cancelUrl' => config('app.url') . '/payment/stripe/cancel',
                'clientReferenceId' => $paymentId,
            ],
            'payment_provider' => 'stripe',
        ];
    }

    public function makePaymentId(Merchant $merchant, User $user): string
    {
        return md5(time() . '_' . $merchant->id . '_' . $user->id);
    }
}
