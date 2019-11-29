<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Merchant\Earning\ActionStoreRequest;
use App\Merchant;
use App\Models\MerchantAction;
use App\Models\Plan;
use App\Repositories\Contracts\ActionRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantActionRestrictionRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Services\Amazon\UploadFile;
use App\Transformers\MerchantActionRestrictionTransformer;
use App\Transformers\MerchantActionTransformer;
use App\Transformers\PlanTransformer;
use App\Transformers\TierTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Stripe as StripeService;

class MerchantPlanController extends Controller
{
    protected $plans;

    protected $payments;

    protected $stripeService;

    public function __construct(PlanRepository $plans, PaymentRepository $payments, StripeService $stripeService)
    {
        $this->plans = $plans;
        $this->payments = $payments;
        $this->stripeService = $stripeService;
    }

    public function getCurrentPlan(Request $request, Merchant $merchant)
    {
        $plan = $merchant->plan();

        return fractal($plan)->transformWith(new PlanTransformer())->toArray();
    }

    public function upgradePlan(Request $request, Merchant $merchant)
    {
        $planId = $request->input('plan_id');
        $paymentPeriod = $request->input('yearly') ? 365 : 30;

        if (! $planId) {
            return response()->json(['message' => 'Account was not upgraded. You need to choose subscription plan.'], 422);
        }

        try {
            $plan = $this->plans->find($planId);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Account was not upgraded. Selected plan does not exist.'], 422);
        }

        // Set provider for payment process
        $paymentProvider = null;
        if (isset($merchant->payment_provider) && in_array(strtolower($merchant->payment_provider), [
                'shopify',
                'stripe',
            ])) {
            $paymentProvider = strtolower($merchant->payment_provider);
        }

        if ($paymentProvider == 'stripe') {
            // Pay via Stripe
            return $this->payViaStripe($merchant, $request->user(), $plan, $paymentPeriod);
        }

        // Get merchant store integration
        try {
            $storeIntegration = app('merchant_service')->getStoreIntegration($merchant->id);
        } catch (\Exception $exception) {
            // No store integration
        }

        if (is_null($paymentProvider) || $paymentProvider == 'shopify') {
            // Verify Shopify integration
            if (! isset($storeIntegration) || $storeIntegration->slug != 'shopify') {
                // Pay via Stripe
                return $this->payViaStripe($merchant, $request->user(), $plan, $paymentPeriod);
            }

            // Pay via Shopify
            return $this->payViaShopify($merchant, $request->user(), $plan, $paymentPeriod, $storeIntegration, ($request->input('testPayment') ? 1 : 0));
        }
    }

    private function payViaShopify($merchant, $user, $plan, $paymentPeriod = 30, $storeIntegration, $testMode = false)
    {
        if (! $storeIntegration || $storeIntegration->slug != 'shopify') {
            return response()->json(['message' => 'Account was not upgraded. You need to setup Shopify integration first.'], 405);
        }

        $api = app('shopify_api')->setup();
        $api->setShop($storeIntegration->pivot->external_id);
        $api->setAccessToken($storeIntegration->pivot->token);

        if ($paymentPeriod == 365) {
            try {
                // Create a new ApplicationCharge
                $chargeCreateResponse = $api->rest('POST', '/admin/application_charges.json', [
                    'application_charge' => [
                        'name'       => 'Lootly '.$plan->name,
                        'price'      => floatval(round($plan->price * 12 * 0.9)),
                        'return_url' => env('APP_URL').'/payment/shopify/charge/accept',
                        'test'       => $testMode ? true : null,
                    ],
                ])->body->application_charge;

                $responseData = [
                    'confirmation_url' => $chargeCreateResponse->confirmation_url,
                    'id'               => $chargeCreateResponse->id,
                    'response'         => $chargeCreateResponse,
                    'payment_provider' => 'shopify',
                ];

                $payment = $this->payments->create([
                    'merchant_id' => $merchant->id,
                    'user_id'     => $user->id,
                    'service'     => 'shopify',
                    'payment_id'  => $chargeCreateResponse->id,
                    'status'      => 'pending',
                    'price'       => floatval($chargeCreateResponse->price),
                    'plan_id'     => $plan->id,
                    'type'        => 'yearly',
                ]);

                return response()->json(['data' => $responseData], 200);
            } catch (\Exception $exception) {
                throw $exception;
            }
        } else {
            try {
                // Create a new RecurringApplicationCharge
                $chargeCreateResponse = $api->rest('POST', '/admin/recurring_application_charges.json', [
                    'recurring_application_charge' => [
                        'name'       => 'Lootly '.$plan->name,
                        'price'      => floatval($plan->price),
                        'return_url' => env('APP_URL').'/payment/shopify/charge/accept',
                        'test'       => $testMode ? true : null,
                    ],
                ])->body->recurring_application_charge;

                $responseData = [
                    'confirmation_url' => $chargeCreateResponse->confirmation_url,
                    'id'               => $chargeCreateResponse->id,
                    'response'         => $chargeCreateResponse,
                    'payment_provider' => 'shopify',
                ];

                $payment = $this->payments->create([
                    'merchant_id' => $merchant->id,
                    'user_id'     => $user->id,
                    'service'     => 'shopify',
                    'payment_id'  => $chargeCreateResponse->id,
                    'status'      => 'pending',
                    'price'       => floatval($chargeCreateResponse->price),
                    'plan_id'     => $plan->id,
                    'type'        => 'monthly',
                ]);

                return response()->json(['data' => $responseData], 200);
            } catch (\Exception $exception) {
                throw $exception;
            }
        }
    }

    private function payViaStripe($merchant, $user, $plan, $paymentPeriod = 30)
    {
        $stripePlans = config('services.stripe.plans');

        if (! isset($stripePlans[$plan->type])) {
            throw new \Exception("Stripe plan is not provided");
        }

        if ($paymentPeriod == 365) {
            if (! isset($stripePlans[$plan->type]['yearly'])) {
                throw new \Exception("Stripe plan does not exists");
            }

            try {
                $paymentId = $this->stripeService->makePaymentId($merchant, $user);

                $responseData = $this->stripeService->getCheckoutRequestData(
                    $stripePlans[$plan->type]['yearly'],
                    $paymentId
                );

                $this->payments->create([
                    'merchant_id' => $merchant->id,
                    'user_id'     => $user->id,
                    'service'     => 'stripe',
                    'payment_id'  => $paymentId,
                    'status'      => 'pending',
                    'price'       => floatval($plan->price),
                    'plan_id'     => $plan->id,
                    'type'        => 'yearly',
                ]);

                return response()->json(['data' => $responseData], 200);

            } catch (\Exception $exception) {
                throw $exception;
            }

        } else {
            if (! isset($stripePlans[$plan->type]['monthly'])) {
                throw new \Exception("Stripe plan does not exists");
            }

            try {
                $paymentId = $this->stripeService->makePaymentId($merchant, $user);

                $responseData = $this->stripeService->getCheckoutRequestData(
                    $stripePlans[$plan->type]['monthly'],
                    $paymentId
                );

                $this->payments->create([
                    'merchant_id' => $merchant->id,
                    'user_id'     => $user->id,
                    'service'     => 'stripe',
                    'payment_id'  => $paymentId,
                    'status'      => 'pending',
                    'price'       => floatval($plan->price),
                    'plan_id'     => $plan->id,
                    'type'        => 'monthly',
                ]);

                return response()->json(['data' => $responseData], 200);

            } catch (\Exception $exception) {
                throw $exception;
            }
        }
    }
}