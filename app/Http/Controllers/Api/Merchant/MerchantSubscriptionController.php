<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Transformers\SubscriptionTransformer;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Models\Subscription;
use App\Services\Stripe as StripeService;

class MerchantSubscriptionController extends Controller
{
    private $userRepository;

    private $merchantRepository;

    private $subscriptionRepository;

    private $stripeService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        MerchantRepository $merchantRepository,
        SubscriptionRepository $subscriptionRepository,
        StripeService $stripeService
    ) {
        $this->userRepository = $userRepository;
        $this->merchantRepository = $merchantRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->stripeService = $stripeService;
    }

    public function getCurrentSubscription(Request $request, Merchant $merchant)
    {
        $subscription = $merchant->plan_subscription ?? null;

        return fractal($subscription)->transformWith(new SubscriptionTransformer())->toArray();
    }

    public function cancelTrialSubscription(Request $request, Merchant $merchant)
    {
        $user = $request->user();

        $merchantSubscription = $this->subscriptionRepository->findWhereFirst([
            'merchant_id' => $merchant->id,
        ]);

        if ($merchantSubscription) {
            $this->subscriptionRepository->clearEntity();
            $this->subscriptionRepository->update($merchantSubscription->id, [
                'status' => Subscription::STATUS_CANCELLED,
            ]);

            // Notification Stripe that the trial is cancelled
            $this->stripeService->cancelSubscriptionTrial($merchantSubscription);
        }

        return response()->json([
            'data' => [],
        ], 200);
    }
}
