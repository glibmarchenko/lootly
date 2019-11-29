<?php

namespace App\Http\Controllers\Api\Widget;

use App\Events\CustomerSpentPointsForReward;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\CustomerTransactionFlagRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\LowestPointsRequiredFirst;
use App\Repositories\Eloquent\Criteria\SharedLock;
use App\Repositories\Eloquent\Criteria\SpentPoints;
use App\Repositories\Eloquent\Criteria\UpdateLock;
use App\Transformers\CouponTransformer;
use App\Transformers\MerchantRewardTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WidgetRewardController extends Controller
{
    protected $merchantRewards;

    protected $customers;

    protected $points;

    protected $customerTransactionFlags;

    public function __construct(
        MerchantRewardRepository $merchantRewards,
        CustomerRepository $customers,
        PointRepository $points,
        CustomerTransactionFlagRepository $customerTransactionFlags
    ) {
        $this->merchantRewards = $merchantRewards;
        $this->customers = $customers;
        $this->points = $points;
        $this->customerTransactionFlags = $customerTransactionFlags;
    }

    public function getRewards(Request $request)
    {
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id'))) {
            return response()->json([], 200);
        }

        $active_merchant_rewards = $this->merchantRewards->withCriteria([
            new EagerLoad(['reward']),
            new LowestPointsRequiredFirst(),
        ])->findWhere([
            'merchant_id' => $request->get('merchant_id'),
            'active_flag' => 1,
        ]);

        return fractal($active_merchant_rewards)->transformWith(new MerchantRewardTransformer)->toArray();
    }

    public function getReward(Request $request, $id)
    {
        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id')) || ! trim($id)) {
            return response()->json([], 404);
        }

        try {
            $active_merchant_reward = $this->merchantRewards->withCriteria([
                new ByMerchant($request->get('merchant_id')),
                new EagerLoad(['reward']),
            ])->findWhereFirst([
                'active_flag' => 1,
                'id'          => $id,
            ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 404);
        }

        return fractal($active_merchant_reward)->transformWith(new MerchantRewardTransformer)->toArray();
    }

    public function redeemReward(Request $request, $rewardId)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json(['message' => 'Invalid request data'], 403);
        }

        $spend_points_record = null;

        try {

            $active_merchant_reward = $this->merchantRewards->withCriteria([
                new EagerLoad(['reward']),
            ])->findWhereFirst([
                'id'          => $rewardId,
                'merchant_id' => $request->get('merchant_id'),
                'active_flag' => 1,
            ]);

            $this->customers->transaction(function () use ($request, $rewardId, $active_merchant_reward, &$spend_points_record) {

                $lockTransaction = $this->customerTransactionFlags->withCriteria([
                    new UpdateLock(),
                ])->updateOrCreate(['customer_id' => $request->get('customer_id')], ['locked' => 1]);

                $points = $this->points->withCriteria([
                    new ByCustomer($request->get('customer_id')),
                    new SharedLock(),
                    // Optional
                ])->all();

                $points_balance = $points->sum('point_value');

                if (isset($active_merchant_reward->reward) && $active_merchant_reward->reward->slug == 'variable-amount') {
                    if (! $request->get('points') || ! trim($request->get('points')) || intval($request->get('points')) <= 0) {
                        throw new \Exception('Invalid request data');
                    }
                    $variable_points = intval($request->get('points'));

                    $points_required_per_unit = intval($active_merchant_reward->points_required);
                    if ($variable_points < $points_required_per_unit) {
                        throw new \Exception('Invalid request data');
                    }
                    $points_required = floor($variable_points / $points_required_per_unit) * $points_required_per_unit;
                } else {
                    $points_required = intval($active_merchant_reward->points_required);
                }

                if ($points_required > $points_balance) {
                    throw new \Exception('Not enough point to redeem chosen reward');
                }

                // Subtract points
                $this->points->clearEntity();
                $spend_points_record = $this->points->create([
                    'merchant_id'        => $request->get('merchant_id'),
                    'customer_id'        => $request->get('customer_id'),
                    'point_value'        => $points_required * -1,
                    'merchant_reward_id' => $active_merchant_reward->id,
                    'title'              => $active_merchant_reward->reward_name,
                    'type'               => $active_merchant_reward->reward_type,
                ]);

                // Unlocking transaction
                $this->customerTransactionFlags->clearEntity();
                $this->customerTransactionFlags->update($lockTransaction->id, ['locked' => 0]);
            });
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Cannot perform point deducting transaction',
                'error'   => $exception->getMessage(),
            ], 405);
        }

        // Generate coupon
        try {
            if ($spend_points_record) {
                $newCoupon = app('coupon_service')->generateRewardCoupon($spend_points_record->merchant_reward_id, $spend_points_record->customer_id, $spend_points_record->id, null, ['available_for_owner_only' => true]);
                if (isset($active_merchant_reward)) {
                    event(new CustomerSpentPointsForReward($active_merchant_reward, $spend_points_record, $newCoupon));
                }
            } else {
                //$newCoupon = app('coupon_service')->generateRewardCoupon($spend_points_record->merchant_reward_id, $spend_points_record->customer_id);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Cannot generate coupon for chosen reward',
                'error'   => $exception->getMessage(),
            ], 405);
        }

        // Update point record
        if (isset($newCoupon) && $newCoupon) {
            $this->points->clearEntity();
            try {
                $this->points->update($spend_points_record->id, [
                    'coupon_id' => $newCoupon->id,
                ]);
            } catch (\Exception $e) {
                // Hmm.. something wrong
            }

            return fractal($newCoupon)->transformWith(new CouponTransformer)->toArray();
        } else {
            return response()->json([
                'message' => 'Coupon for chosen reward was not generated successfully',
            ], 405);
        }
    }
}
