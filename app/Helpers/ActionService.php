<?php

namespace App\Helpers;

use App\Events\CustomerEarnedPointsForAction;
use App\Models\Customer;
use App\Models\MerchantAction;
use App\Models\Point;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\CreatedBetween;
use App\Repositories\Eloquent\Criteria\EarnedPoints;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ActionService
{
    protected $points;

    protected $merchantActions;

    public function __construct(
        PointRepository $points,
        MerchantActionRepository $merchantActions
    ) {

        $this->points = $points;
        $this->merchantActions = $merchantActions;

        $this->orderModel = new OrderRepository();
    }

    public function validateAndCreditPoints(MerchantAction $merchantAction, Customer $customer, array $actionData = [])
    {
        if (!(bool)$merchantAction->active_flag) {
            return null;
        }


        $actionType = $merchantAction->action->type ?? null;
        $actionName = $merchantAction->action->name ?? '';

        $pointStructure = [
            'merchant_id'             => $merchantAction->merchant_id,
            'customer_id'             => $customer->id,
            'title'                   => $actionName,
            'coupon_id'               => $actionData['local_order_obj']->coupon_id ?? null,
            'order_id'                => $actionData['local_order_obj']->id ?? null,
            'total_order_amount'      => $actionData['local_order_obj']->total_price ?? 0,
            'rewardable_order_amount' => $actionData['local_order_obj']->total_price ?? 0,
            'type'                    => $actionType,
            'tier_multiplier'         => $customer->tier->multiplier ?? 1,
            'referral_id'             => $actionData['local_order_obj']->referring_customer_id ?? null,
        ];

        $pointStructure['rewardable_order_amount'] = $actionData['order_data']['subtotal_price'] ?? $pointStructure['total_order_amount'] ?? 0;
        //Log::info( $pointStructure['rewardable_order_amount'] );

        if (isset($actionData['integration_settings'])) {
            if (isset($actionData['order_data']['subtotal_price'])) {

                // Shipping
                $shipping = $actionData['order_data']['total_shipping'] ?? 0;
                if ($shipping) {
                    if (isset($actionData['integration_settings']['order_settings']['include_shipping']) && boolval($actionData['integration_settings']['order_settings']['include_shipping'])) {
                        $pointStructure['rewardable_order_amount'] += floatval($shipping);
                    }
                }

                // Taxes
                $taxes = $actionData['order_data']['total_tax'] ?? 0;
                if ($taxes) {
                    if (isset($actionData['integration_settings']['order_settings']['include_taxes']) && boolval($actionData['integration_settings']['order_settings']['include_taxes'])) {
                        $pointStructure['rewardable_order_amount'] += floatval($taxes);
                    }
                }

                // Discounts
                $discounts = $actionData['order_data']['total_discounts'] ?? 0;
                if ($discounts) {
                    if (isset($actionData['integration_settings']['order_settings']['exclude_discounts']) && boolval($actionData['integration_settings']['order_settings']['exclude_discounts'])) {
                        $pointStructure['rewardable_order_amount'] -= floatval($discounts);
                    }
                }
            }
        }
        if ($pointStructure['rewardable_order_amount'] < 0) {
            $pointStructure['rewardable_order_amount'] = 0;
        }
        $pointStructure['rewardable_order_amount'] = round($pointStructure['rewardable_order_amount'], 2);

        $point_value = $merchantAction->point_value;

        if (boolval($merchantAction->is_fixed)) {
            // Credit fixed point amount
            $pointStructure['point_value'] = intval($point_value) * intval($pointStructure['tier_multiplier']);
        } else {
            // Credit price based point amount
            $pointStructure['point_value'] = intval(floor($pointStructure['rewardable_order_amount']) * (intval($point_value) * intval($pointStructure['tier_multiplier'])));
            //Log::info( 'Final points' );
            //Log::info( $pointStructure['point_value'] );
        }

        if ($merchantAction->earning_limit) {

            $earningLimitValue = intval($merchantAction->earning_limit_value);
            $earningLimitType = $merchantAction->earning_limit_type;
            $earningLimitPeriod = $merchantAction->earning_limit_period;

            $toDateTime = null;
            switch ($earningLimitPeriod) {
                case 'lifetime':
                    break;
                case 'year':
                    $toDateTime = Carbon::now()->subYear();
                    break;
                case 'month':
                    $toDateTime = Carbon::now()->subMonth();
                    break;
                case 'week':
                    $toDateTime = Carbon::now()->subWeek();
                    break;
            }

            if ($toDateTime) {
                $earnedPoints = $this->points->withCriteria([
                    new EarnedPoints(),
                    new CreatedBetween($toDateTime, Carbon::now()),
                ])->findWhere([
                    'customer_id'        => $customer->id,
                    'merchant_action_id' => $merchantAction->id,
                ]);
            } else {
                $earnedPoints = $this->points->withCriteria([
                    new EarnedPoints(),
                ])->findWhere([
                    'customer_id'        => $customer->id,
                    'merchant_action_id' => $merchantAction->id,
                ]);
            }
            switch ($earningLimitType) {
                case 'times':
                    $validPointsCount = 0;
                    $rolledBackPointsCount = 0;
                    for ($i = 0; $i < count($earnedPoints); $i++) {
                        if ($earnedPoints[$i]->rollback) {
                            $rolledBackPointsCount++;
                        } else {
                            $validPointsCount++;
                        }
                    }
                    if (($validPointsCount - $rolledBackPointsCount) >= $earningLimitValue) {
                        return null;
                    }
                    break;
                case 'points':
                    $sumEarnedPoints = $earnedPoints->sum('point_value');
                    if ($sumEarnedPoints >= $earningLimitValue) {
                        return null;
                    }
                    $allowed_point_value = $pointStructure['point_value'] - (($sumEarnedPoints + $pointStructure['point_value']) - $earningLimitValue);
                    if ($allowed_point_value < $pointStructure['point_value']) {
                        $pointStructure['point_value'] = $allowed_point_value;
                    }
                    break;
            }
        }

        if (isset($merchantAction->goal) && $merchantAction->goal) {
            // Checking if already completed action
            //$wasCompleted = $this->pointModel->findLatestByMerchantAction($customer, $merchantAction);
            try {
                $wasCompleted = $this->points->withCriteria([
                    new LatestFirst(),
                ])->findWhereFirst([
                    'customer_id'        => $customer->id,
                    'merchant_action_id' => $merchantAction->id,
                ]);
                if ($wasCompleted && $wasCompleted->point_value >= 0) {
                    return null;
                }
            } catch (\Exception $e) {
                //
            }
            $addPointsForGoal = true;
            switch ($merchantAction->goal_unit) {
                case 'money':
                    $totalMoneySpent = $this->orderModel->getTotalSpent($customer);
                    if (floatval($totalMoneySpent) < floatval($merchantAction->goal)) {
                        $addPointsForGoal = false;
                    }
                    break;
                case 'order':
                    $countOrders = $this->orderModel->countValidOrders($customer);
                    if (intval($countOrders) < intval($merchantAction->goal)) {
                        $addPointsForGoal = false;
                    }
                    break;
                default:
                    $addPointsForGoal = false;
                    break;
                //coupons, points, etc.
            }
            if (! $addPointsForGoal) {
                return null;
            }
        }

        // @todo: check other restrictions and continue or break

        //$point = $this->pointModel->addPointsForAction($customer, $merchantAction, $pointStructure);
        $point = $this->merchantActions->createPoint($merchantAction->id, $pointStructure);

        if (isset($point) && $point) {
            event(new CustomerEarnedPointsForAction($merchantAction, $point));

            return $point;
        } else {
            return null;
        }
    }

    public function validateAndDeductPoints(Point $point)
    {

    }

    public function validateAccountCreateActionAndCreditPoints(MerchantAction $merchantAction, Customer $customer)
    {
        if (! boolval($merchantAction->active_flag)) {
            return null;
        }

        $actionType = $merchantAction->action->type ?? null;
        $actionName = $merchantAction->action->name ?? '';

        $pointStructure = [
            'merchant_id' => $merchantAction->merchant_id,
            'customer_id' => $customer->id,
            'title'       => $actionName,
            'type'        => $actionType,
        ];

        $point_value = $merchantAction->point_value;

        $pointStructure['point_value'] = intval($point_value);

        $point = $this->merchantActions->createPoint($merchantAction->id, $pointStructure);

        if (isset($point) && $point) {
            event(new CustomerEarnedPointsForAction($merchantAction, $point));

            return $point;
        } else {
            return null;
        }
    }

    public function creditPointsForAction(MerchantAction $merchantAction, $customer_id, $data = [])
    {
        if (! boolval($merchantAction->active_flag) || ! isset($merchantAction->action)) {
            return null;
        }

        $actionType = $merchantAction->action->type ?? null;
        $actionName = $merchantAction->action->name ?? '';

        $pointStructure = [
            'merchant_id' => $merchantAction->merchant_id,
            'customer_id' => $customer_id,
            'title'       => $actionName,
            'type'        => $actionType,
        ];

        $point_value = $merchantAction->point_value;

        $pointStructure['point_value'] = (int)$point_value;

        switch ($merchantAction->action->url) {
            case 'create-account':
                break;
            case 'celebrate-birthday':
                break;
            case 'facebook-like':
            case 'facebook-share':
            case 'twitter-follow':
            case 'twitter-share':
            case 'instagram-follow':
            case 'trustspot-review':
            case 'read-content':

                if ($merchantAction->earning_limit) {

                    $earningLimitValue = (int)$merchantAction->earning_limit_value;
                    $earningLimitType = $merchantAction->earning_limit_type;
                    $earningLimitPeriod = $merchantAction->earning_limit_period;

                    $toDateTime = null;
                    switch ($earningLimitPeriod) {
                        case 'lifetime':
                            break;
                        case 'year':
                            $toDateTime = Carbon::now()->subYear();
                            break;
                        case 'month':
                            $toDateTime = Carbon::now()->subMonth();
                            break;
                        case 'week':
                            $toDateTime = Carbon::now()->subWeek();
                            break;
                    }

                    if ($toDateTime) {
                        $earnedPoints = $this->points->withCriteria([
                            new EarnedPoints(),
                            new CreatedBetween($toDateTime, Carbon::now()),
                        ])->findWhere([
                            'customer_id'        => $customer_id,
                            'merchant_action_id' => $merchantAction->id,
                        ]);
                    } else {
                        $earnedPoints = $this->points->withCriteria([
                            new EarnedPoints(),
                        ])->findWhere([
                            'customer_id'        => $customer_id,
                            'merchant_action_id' => $merchantAction->id,
                        ]);
                    }
                    switch ($earningLimitType) {
                        case 'times':
                            $validPointsCount = 0;
                            $rolledBackPointsCount = 0;
                            for ($i = 0; $i < count($earnedPoints); $i++) {
                                if ($earnedPoints[$i]->rollback) {
                                    $rolledBackPointsCount++;
                                } else {
                                    $validPointsCount++;
                                }
                            }
                            if (($validPointsCount - $rolledBackPointsCount) >= $earningLimitValue) {
                                throw new \Exception('Action has been already completed');
                            }
                            break;
                        case 'points':
                            $sumEarnedPoints = $earnedPoints->sum('point_value');
                            if ($sumEarnedPoints >= $earningLimitValue) {
                                throw new \Exception('Action has been already completed');
                            }
                            $allowed_point_value = $pointStructure['point_value'] - (($sumEarnedPoints + $pointStructure['point_value']) - $earningLimitValue);
                            if ($allowed_point_value < $pointStructure['point_value']) {
                                $pointStructure['point_value'] = $allowed_point_value;
                            }
                            break;
                    }
                }

                /*$alreadyCreditedPointForAction = $this->points->withCriteria([
                    new EarnedPoints(),
                    new LatestFirst(),
                    new ByCustomer($customer_id),
                ])->findWhere([
                    'merchant_action_id' => $merchantAction->id,
                ]);
                if (count($alreadyCreditedPointForAction)) {

                /*if($alreadyCreditedPointForAction[0]->rollback == 0) {
                    throw new \Exception('Action has been already completed');
                }
                    // more checking

                }
                */
                // credit points
                $point = $this->merchantActions->createPoint($merchantAction->id, $pointStructure);
                break;
            case 'custom-earning':
                $point = $this->merchantActions->createPoint($merchantAction->id, $pointStructure);
                break;
        }

        if (isset($point) && $point) {
            event(new CustomerEarnedPointsForAction($merchantAction, $point));

            return $point;
        } else {
            return null;
        }
    }
}
