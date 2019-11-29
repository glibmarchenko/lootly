<?php

namespace App\Repositories;

use App\Merchant;
use App\Models\Order;
use App\Models\Billing;
use App\Models\Coupon;
use Carbon\Carbon;
use App\Traits\QueryBuilderTrait;
use App\Transformers\PopularEarningActionsTransformer;
use App\Transformers\PopularSpendingRewardsTransformer;

class ReportsRepository
{
    use QueryBuilderTrait;

    public function __construct()
    {
        $this->merchantRepo = new MerchantRepository;
        $this->customerRepo = new CustomerRepository;
    }

    /**
     * Get number of new customers which join a $merchant during $period
     * @param \DatePeriod $period period during which customers will be count
     * @param Merchant|null $merchant Merchant model
     * @param array|null see documentation in App\Traits\QueryBuilderTrait
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCustomersStatistic(\DatePeriod $period, Merchant $merchant = null, array $filters = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));
        $customers = $merchant->customers()
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end);

        if (!empty($filters)) {
            $customers = $this->applyFilters($customers, $filters);
        }

        return $customers;
    }

    /**
     * Get number of points which was earned during $period
     * @param \DatePeriod $period period during which earned points will be count
     * @param Merchant $merchant Merchant model
     * @param array|null see documentation in App\Traits\QueryBuilderTrait
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEarnedStatistic(\DatePeriod $period, Merchant $merchant = null, array $filters = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));

        $points = $merchant->points()
            ->where('point_value', '>', 0)
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end);

        if (!empty($filters)) {
            $points = $this->applyFilters($points, $filters);
        }

        return $points;
        // return $merchant->points->filter(function($point) use ($start, $end){
        //     if($point->point_value > 0){
        //         if($point->created_at > $start && $point->created_at < $end){
        //             return $point->point_value;
        //         }
        //     }
        // });
    }

    /**
     * Get number of points which was spent during $period
     * @param \DatePeriod $period period during which spent points will be count
     * @param Merchant $merchant Merchant model
     * @return \Illuminate\Support\Collection
     */
    public function getSpentStatistic(\DatePeriod $period, Merchant $merchant = null)
    {
        // if(empty($merchant)){
        //     $merchant = $this->merchantRepo->getCurrent();
        // }

        // $start = Carbon::instance($period->getStartDate());
        // $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));

        // return $merchant->points->filter(function($point) use ($start, $end){
        //     if($point->type == 'Spent'){
        //         if($point->created_at > $start && $point->created_at < $end){
        //             return $point->point_value;
        //         }
        //     }
        // });
    }

    /**
     * Get number of customer`s activities during $period
     * @param \DatePeriod $period period during which spent points will be count
     * @param Merchant $merchant Merchant model
     * @param array|null see documentation in App\Traits\QueryBuilderTrait
     *
     * @return array
     */
    public function getActivitiesStatistic(\DatePeriod $period, Merchant $merchant = null, array $filters = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));

        $points = $merchant->points()
            ->where('point_value', '>', 0)
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end);

        if (!empty($filters)) {
            $points = $this->applyFilters($points, $filters);
        }
        return $points;
        // return $merchant->points->filter(function($point) use ($start, $end){
        //     if($point->point_value > 0){
        //         if($point->created_at > $start && $point->created_at < $end){
        //             return $point->point_value;
        //         }
        //     }
        // });
    }

    /**
     * Get value of generated by $merchant's customers during $period
     * @param \DatePeriod $period period during which spent points will count
     * @param Merchant $merchant Merchant model
     * @param array|null see documentation in App\Traits\QueryBuilderTrait
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getValueGeneratedStatistic(\DatePeriod $period, Merchant $merchant = null, array $filters = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));
        $orders = Order::join('customers', 'customers.id', '=', 'orders.customer_id')
            ->select('orders.*', 'customers.merchant_id')
            ->where('merchant_id', '=', $merchant->id)
            ->where('orders.created_at', '>', $start)
            ->where('orders.created_at', '<', $end)
            ->where(function ($query) {
                $query->whereNotNull('coupon_id')
                    ->orWhere('orders.referral_slug', '!=', null);
            });

        if (!empty($filters)) {
            $orders = $this->applyFilters($orders, $filters);
        }

        return $orders;
    }

    /**
     * Get last $limit of points for $merchant
     * @param Merchant $merchant Merchant model
     * @param int $limit limit of records
     * @return \Illuminate\Support\Collection
     */
    public function getPoints(Merchant $merchant = null, $limit = 1000)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        return $merchant->points()
            ->whereNull('merchant_reward_id')
            ->with(['reward', 'customer'])
            ->with('action')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get last $limit of orders for $merchant
     * @param Merchant $merchant Merchant model
     * @param int $limit limit of records
     * @return \Illuminate\Support\Collection
     */
    public function getOrders(Merchant $merchant = null, $limit = 1000)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        return Order::join('customers', 'customers.id', '=', 'orders.customer_id')
            ->select('orders.*', 'customers.merchant_id', 'customers.name')
            ->where('merchant_id', '=', $merchant->id)
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get last $limit of orders with referrals for $merchant
     * @param Merchant $merchant Merchant model
     * @param int $limit limit of records
     * @return \Illuminate\Support\Collection
     */
    public function getReferralOrders(Merchant $merchant = null, $limit = 1000)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        return Order::join('customers', 'customers.id', '=', 'orders.customer_id')
            ->select('orders.*', 'customers.merchant_id', 'customers.name')
            ->where('merchant_id', '=', $merchant->id)
            ->whereNotNull('referring_customer_id')
            ->with('referral')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get value of generated by $merchant's customers during $period
     * @param \DatePeriod $period period during which spent points will count
     * @param Merchant $merchant Merchant model
     * @param array|null see documentation in App\Traits\QueryBuilderTrait
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getReferralGeneratedStatistic(\DatePeriod $period, Merchant $merchant = null, array $filters = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));
        $orders = Order::join('customers', 'customers.id', '=', 'orders.customer_id')
            ->select('orders.*', 'customers.merchant_id')
            ->where('merchant_id', '=', $merchant->id)
            ->where('orders.created_at', '>', $start)
            ->where('orders.created_at', '<', $end)
            ->where(function ($query) {
                $query->whereNotNull('coupon_id')
                    ->whereNotNull('orders.referring_customer_id')
                    ->orWhere('orders.referral_slug', '!=', null);
            });

        if (!empty($filters)) {
            $orders = $this->applyFilters($orders, $filters);
        }

        return $orders;
    }

    /**
     * Get last limit of rewards for merchant
     * @param Merchant|null $merchant
     * @param int $limit limit of records
     * @return \Illuminate\Support\Collection
     */
    public function getRewards(Merchant $merchant = null, $limit = 1000)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }
        return $merchant->points()
            ->whereNotNull('merchant_reward_id')
            ->with(['customer', 'reward', 'action'])
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();
    }

    /**
     * Get last $limit of customers activity for $merchant
     * @param Merchant|null $merchant Merchant model
     * @param \DatePeriod|null $period for which the data will be collected
     * @param array|null see documentation in App\Traits\QueryBuilderTrait
     *
     * @return \Illuminate\Support\Collection
     */
    public function getActivity(Merchant $merchant = null, \DatePeriod $period = null, array $filters = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        if (!$period) {
            $points = $merchant->points();
        } else {
            $start = Carbon::instance($period->getStartDate());
            $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));
            $points = $merchant->points()
                ->where('created_at', '>', $start)
                ->where('created_at', '<', $end);
        }

        if (!empty($filters)) {
            $points = $this->applyFilters($points, $filters);
        }
        return $points;
    }

    /**
     * Get billings during $period
     * @param \DatePeriod $period period during which billings will be collected
     * @param Merchant $merchant Merchant model
     * @return \Illuminate\Support\Collection
     */
    public function getBillingsStatistic(\DatePeriod $period, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));

        return Billing::query()
            ->where('merchant_id', '=', 1)
            ->where('date', '>', $start)
            ->where('date', '<', $end)
            ->with('plan')
            ->get();
    }

    /**
     * Get issued rewards during $period
     * @param \DatePeriod $period period during which coupons will be collected
     * @param Merchant $merchant Merchant model
     * @param bool get data only for $isUsed coupons otherwise get for all
     * @param bool include rewards and orders
     * @return \Illuminate\Support\Collection
     */
    public function getCouponsStatistic(\DatePeriod $period, Merchant $merchant = null, bool $isUsed = true, bool $includeRelations = false)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate())->startOfDay();
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()))->endOfDay();

        $query = Coupon::query()
            ->where('merchant_id', '=', $merchant->id)
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end);

        if ($isUsed) {
            $query->where('is_used', '=', 1);
        }
        if ($includeRelations) {
            $query->with('merchant_reward')->with('order');
        }
        return $query->get();
    }

    /**
     * Calculate invested value during $period
     * @param \DatePeriod $period period during which the value will be calculated
     * @param Merchant $merchant Merchant model
     * @param string|null set type of returned values:
     *      null - return total value
     *      'separately' - return each discount value separately
     *      'array' - return array of pairs 'sum' and 'created_at'
     * @return \Illuminate\Support\Collection|array
     */
    public function calculateInvestmentValue(\DatePeriod $period, Merchant $merchant = null, string $returnType = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $coupons = $this->getCouponsStatistic($period, $merchant, true, true);
        $orderDiscounts = 0;
        $freeProductDiscounts = 0;
        $freeShippingDiscounts = 0;
        $totalResults = [];
        foreach ($coupons as $coupon) {
            if ($coupon->merchant_reward->rewards_type == 'Free shipping') {
                $freeShippingDiscounts += $coupon->order->refunded_total;
            } elseif ($coupon->merchant_reward->rewards_type == 'Free product') {
                $freeProductDiscounts += $coupon->order->refunded_total;
            } else {
                $orderDiscounts += $coupon->order->refunded_total;
            }
            $totalResults[] = [
                'sum' => $coupon->order->refunded_total,
                'created_at' => $coupon->created_at
            ];
        }

        $billings = $this->getBillingsStatistic($period, $merchant);
        if (!empty($billings) && isset($billings)) {
            $lootlyPlanCost = $billings->sum('price');
            foreach ($billings as $billing) {
                $totalResults[] = [
                    'sum' => $billing->price,
                    'created_at' => $billing->created_at
                ];
            }
        } else {
            $lootlyPlanCost = 0;
        }
        if ($returnType == 'separately') {
            return [
                'orderDiscounts' => $orderDiscounts,
                'freeShippingDiscounts' => $freeShippingDiscounts,
                'freeProductDiscounts' => $freeProductDiscounts,
                'lootlyPlanCost' => $lootlyPlanCost
            ];
        } elseif ($returnType == 'array') {
            return $totalResults;
        } else {
            return $orderDiscounts + $freeProductDiscounts + $freeShippingDiscounts + $lootlyPlanCost;
        }
    }

    /**
     * Collect all merchant actions sorted by points
     * @param \DatePeriod $period with DateInterval in days,
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getPopularEarningActions(\DatePeriod $period, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate())->startOfDay();
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()))->endOfDay();

        $merchantActions = $merchant->merchant_actions()
            ->whereHas('point', function ($query) use ($start, $end) {
                $query->where('created_at', '>', $start)
                    ->where('created_at', '<', $end);
            })
            ->get()
            ->sortByDesc(function ($action, $key) {
                if (empty($action->point))
                    return 0;

                return $action->point->sum('point_value');
            });

        $response = fractal()->collection($merchantActions)
            ->parseIncludes(['action', 'points'])
            ->transformWith(new PopularEarningActionsTransformer($period))
            ->toArray()['data'];

        $adminPoints = $merchant->points
            ->where('merchant_action_id', '=', null)
            ->where('merchant_reward_id', '=', null)
            ->where('point_value', '>', 0)
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end);

        $adminAction = [];
        $adminAction[] = [
            'name' => 'Admin Award',
            "action_type" => "Admin Awarded Points",
            "reward" => "Points",
            "points_earned" => $adminPoints->sum('point_value'),
            "completed_actions" => $adminPoints->count(),
        ];

        return array_merge($response, $adminAction);
    }

    /**
     * Collect all merchant actions sorted by points
     * @param \DatePeriod $period with DateInterval in days,
     * @param Merchant $merchant for which data will be collected
     * @return array
     */
    public function getPopularSpendingRewards(\DatePeriod $period, Merchant $merchant = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate())->startOfDay();
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()))->endOfDay();

        $merchantRewards = $merchant->rewards()
            ->whereHas('coupons', function ($query) use ($start, $end) {
                $query->where('created_at', '>', $start)
                    ->where('created_at', '<', $end)
                    ->where('is_used', '=', 1);
            })
            ->get()
            ->sortByDesc(function ($reward, $key) {
                if (empty($reward->coupons))
                    return 0;

                return $reward->coupons->count();
            });

        return fractal()->collection($merchantRewards)
            ->parseIncludes(['action', 'points'])
            ->transformWith(new PopularSpendingRewardsTransformer($period))
            ->toArray()['data'];
    }

    /**
     * Collect shares done by customers of the $merchant
     * @param \DatePeriod $period with DateInterval in days,
     * @param Merchant $merchant for which data will be collected
     * @param string $type of share
     * @return array
     */
    public function getShares(\DatePeriod $period, Merchant $merchant = null, string $type = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));
        $shares = $merchant->shares
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end);
        if ($type == null || \stripos($type, 'All') !== false) {
            return $shares;
        }

        return $shares->filter(function ($share) use ($type) {
            return $share->shared_to == $type;
        });
    }

    /**
     * Collect clicks done by customers of the $merchant
     * @param \DatePeriod $period with DateInterval in days,
     * @param Merchant $merchant for which data will be collected
     * @param string $type of share
     * @return array
     */
    public function getClicks(\DatePeriod $period, Merchant $merchant = null, string $type = null)
    {
        if (empty($merchant)) {
            $merchant = $this->merchantRepo->getCurrent();
        }

        $start = Carbon::instance($period->getStartDate());
        $end = Carbon::instance($period->getStartDate()->add($period->getDateInterval()));

        $clicks = $merchant->clicks
            ->where('created_at', '>', $start)
            ->where('created_at', '<', $end);
        if ($type == null || stripos($type, 'All') !== false) {
            return $clicks;
        }

        return $clicks->filter(function ($click) use ($type) {
            return $click->clicked_from == $type;
        });
    }

}
