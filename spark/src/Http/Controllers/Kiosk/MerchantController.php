<?php

namespace Laravel\Spark\Http\Controllers\Kiosk;

use Illuminate\Http\Request;
use Laravel\Spark\Http\Controllers\Controller;
use Laravel\Spark\Repositories\MerchantRepository;
use Laravel\Spark\Models\Filters\MerchantFilters;
use Laravel\Spark\Http\Resources\MerchantCollection;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\PlansRepository;
use App\Models\Subscription;

class MerchantController extends Controller
{
    protected $merchants;

    protected $subscriptions;

    protected $plans;

    public function __construct(
        MerchantRepository $merchants,
        SubscriptionRepository $subscriptions,
        PlansRepository $plans
    ) {
        $this->merchants = $merchants;
        $this->subscriptions = $subscriptions;
        $this->plans = $plans;

        $this->middleware('auth');
        $this->middleware('dev');
    }

    public function get(MerchantFilters $filters, Request $request)
    {
        $limit = $request->input('limit', 10);

        $merchants = $this->merchants->getWithOwner();

        return (new MerchantCollection($merchants->filter($filters)->paginate($limit)));
    }

    public function update($id, Request $request)
    {
        $merchant = $this->merchants->findOrFail($id);

        $request->validate([
            'owner.email' => 'required|email|unique:users,email,' . $merchant->owner->id,
            'plan.id' => 'nullable|integer|exists:plans,id',
        ]);

        $merchant->owner->update([
            'email' => $request->input('owner.email')
        ]);

        $planId = $request->input('plan.id', null);

        if ($planId !== null) {
            $plan = $this->plans->findOrFail($planId);

            $this->subscriptions->updateOrCreate([
                'merchant_id' => $merchant->id,
            ], [
                'merchant_id' => $merchant->id,
                'name' => $plan->name,
                'plan_id' => $plan->id,
                'user_id' => $merchant->owner->id,
                'length' => 30,
                'status' => Subscription::STATUS_ACTIVE,
            ]);
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Saved successfully!',
        ]);
    }
}
