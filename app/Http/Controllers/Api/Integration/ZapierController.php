<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use App\Models\ZapierHook;
use App\Repositories\CustomerRepository;
use App\Repositories\Eloquent\Criteria\UpdateLock;
use App\Repositories\Contracts\CustomerTransactionFlagRepository;
use App\Repositories\MerchantActionRepository;
use App\Repositories\MerchantDetailRepository;
use App\Repositories\MerchantRewardRepository;
use App\Repositories\PointRepository;
use App\Repositories\Contracts\PointRepository as Points;
use App\Repositories\Contracts\CustomerRepository as Customers;
use Illuminate\Http\Request;

class ZapierController extends Controller
{

    protected $customerTransactionFlags;
    protected $merchantDetailsRepo;
    protected $actionsRepo;
    protected $customerRepo;
    protected $pointsRepo;
    protected $rewardRepo;
    protected $customers;
    protected $merchant;
    protected $points;

    public function __construct(
        MerchantDetailRepository $merchantDetails,
        CustomerRepository $customerRepository,
        PointRepository $pointRepository,
        MerchantRewardRepository $rewardRepository,
        MerchantActionRepository $actionsRepository,
        Points $points,
        Customers $customers,
        CustomerTransactionFlagRepository $customerTransactionFlags
    )
    {
        $this->merchantDetailsRepo = $merchantDetails;
        $this->customerRepo = $customerRepository;
        $this->pointsRepo = $pointRepository;
        $this->rewardRepo = $rewardRepository;
        $this->actionsRepo = $actionsRepository;
        $this->points = $points;
        $this->customers = $customers;
        $this->customerTransactionFlags = $customerTransactionFlags;

        $merchantDetail = $merchantDetails->findBy('api_key', request('api_key'));

        if (!$merchantDetail) {
            abort(404);
        }

        $this->merchant = $merchantDetail->merchant;
    }

    public function auth()
    {
        return response()->json();
    }

    public function subscribe(Request $request)
    {
        ZapierHook::query()->create([
            'user_id' => $this->merchant->id,
            'url' => $request->get('hookUrl'),
            'event' => $request->get('event'),
        ]);

        return response()->json();
    }

    public function unsubscribe(Request $request)
    {

        $hook = ZapierHook::query()->findOrFail($request->id);

        $hook->delete();

        return response()->json();
    }

    public function point_trigger(Request $request)
    {
        $event = $request->event;

        $email = $request->email;

        $customerObj = $this->customerRepo->getByEmail($email);

        if (!$customerObj) {
            abort(404);
        }

        $actions = $this->actionsRepo->getActiveByType($this->merchant, 'Custom');

        if ($actions) {
            $action = $actions->filter(function ($action) use ($event) {
                return $action->zap_name == $event;
            })->first();
        }

        if (isset($action)) {
            app('action_service')->creditPointsForAction($action, $customerObj->id);

            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'not_found_action']);
    }

    public function deduct_point_trigger(Request $request)
    {
        $email = $request->get('email');

        $amount = $request->get('amount');

        $customerObj = $this->customerRepo->getByEmail($email);

        if (!$customerObj) {
            abort(404);
        }

        app('customer_service')->givePoints($this->merchant->id, $customerObj->id, -abs($amount), [
            'title' => 'Unsubscribed from MailChimp',
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function reward_trigger(Request $request)
    {
        $event = $request->get('event');

        $email = $request->get('email');

        $customerObj = $this->customerRepo->getByEmail($email);

        if (!$customerObj) {
            abort(404);
        }

        if ($rewards = $this->rewardRepo->all($this->merchant)) {
            $reward = $rewards->filter(function ($reward) use ($event) {
                return $reward->zap_key == $event;
            })->first();
        }

        if (isset($reward)) {

            $spend_point_record = null;

            try {

                $this->customers->transaction(function () use ($customerObj, $reward, &$spend_point_record) {

                    $lockTransaction = $this->customerTransactionFlags->withCriteria([
                        new UpdateLock(),
                    ])->updateOrCreate(['customer_id' => $customerObj->id], ['locked' => 1]);

                    // Subtract points
                    $this->points->clearEntity();
                    $spend_point_record = $this->points->create([
                        'merchant_id' => $customerObj->merchant_id,
                        'customer_id' => $customerObj->id,
                        'point_value' => 0,
                        'merchant_reward_id' => $reward->id,
                        'title' => $reward->reward_name,
                        'type' => $reward->reward_type,
                    ]);

                    // Unlocking transaction
                    $this->customerTransactionFlags->clearEntity();
                    $this->customerTransactionFlags->update($lockTransaction->id, ['locked' => 0]);
                });

                $spend_point_record_id = $spend_point_record ? $spend_point_record->id : null;

                app('coupon_service')->generateRewardCoupon($reward->id, $customerObj->id, $spend_point_record_id, null, []);
            } catch (\Exception $exception) {
                return response()->json([
                    'Cannot generate discount at this moment. Please try again.',
                    $exception->getMessage(),
                ], 500);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function sample()
    {

        $zap = [
            'email' => "john@smith.com",
            'points' => 100,
            'reason' => "Facebook Like"
        ];

        return response()->json([$zap]);
    }

}
