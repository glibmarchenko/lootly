<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Customer\CustomerUpdateRequest;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\ReferralSettingsRepository;
use App\Repositories\Contracts\ReferralRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\EarnedPoints;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\SpentPoints;
use App\Repositories\Eloquent\Criteria\WithEarnedPointsHotFix;
use App\Repositories\Eloquent\Criteria\WithEarnedPointsInYear;
use App\Repositories\Eloquent\Criteria\WithTierHistory;
use App\Repositories\Eloquent\Criteria\WithTrashedMerchantRewards;
use App\Repositories\Eloquent\Criteria\WithUsedCoupons;
use App\Repositories\RewardCouponRepository;
use App\Transformers\CouponTransformer;
use App\Transformers\CustomerMerchantActionTransformer;
use App\Transformers\CustomerTransformer;
use App\Transformers\MerchantTransformer;
use App\Transformers\PointTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Support\Facades\Log;

class WidgetCustomerController extends Controller
{
    protected $points;

    protected $customers;

    protected $coupons;

    protected $merchants;

    protected $orders;

    protected $merchantActions;

    protected $referralSettings;

    protected $referralRepo;

    protected $merchantRewards;

    protected $rewardCoupon;

    protected $pointSettings;

    public function __construct(
        PointRepository $points,
        CustomerRepository $customers,
        CouponRepository $coupons,
        MerchantRepository $merchants,
        MerchantActionRepository $merchantActions,
        OrderRepository $orders,
        PointSettingsRepository $pointSettings,
        ReferralSettingsRepository $referralSettings,
        ReferralRepository $referralRepo,
        MerchantRewardRepository $merchantRewards,
        RewardCouponRepository $rewardCoupon
    ) {
        $this->points = $points;
        $this->customers = $customers;
        $this->coupons = $coupons;
        $this->merchants = $merchants;
        $this->orders = $orders;
        $this->merchantActions = $merchantActions;
        $this->pointSettings = $pointSettings;
        $this->referralSettings = $referralSettings;
        $this->referralRepo = $referralRepo;
        $this->merchantRewards = $merchantRewards;
        $this->rewardCoupon = $rewardCoupon;
    }

    public function authCheck(Request $request)
    {
        $responseData = [];

        if (! $request->get('merchant_id') || ! trim($request->get('merchant_id'))) {
            return response()->json($responseData, 200);
        }

        try {
            $merchant = $this->merchants->withCriteria([
                new EagerLoad(['detail']),
            ])->find($request->get('merchant_id'));
        } catch (\Exception $exception) {
            //
        }

        if (! isset($merchant) || ! $merchant) {
            return response()->json($responseData, 200);
        }

        $merchantTransformer = new MerchantTransformer();

        $responseData['merchant'] = $merchantTransformer->transform($merchant);
        $responseData['merchant_platform'] = $merchant->integrations->where('status', 1)[0]->slug;
        
        try {
            $referralSettings = $this->referralSettings->findWhereFirst(['merchant_id' => $merchant->id]);
            $responseData['referral_settings'] = $referralSettings;
        } catch (\Exception $exception) {
            //
        }

        if (! $request->get('customer')) {
            return response()->json($responseData, 200);
        }
        $customer_id = $request->get('customer')['id'] ?? null;
        $token = $request->get('customer')['signature'] ?? null;

        $merchant_api_secret = null;
        if (isset($merchant->detail) && isset($merchant->detail->api_secret)) {
            $merchant_api_secret = trim($merchant->detail->api_secret);
        }
        if (! $merchant_api_secret) {
            return response()->json($responseData, 200);
        }
        $sign = md5($customer_id.$merchant_api_secret);
        if ($sign !== $token) {
            return response()->json($responseData, 200);
        }

        try {
            $customer = $this->customers->findWhereFirst([
                'ecommerce_id' => $customer_id,
                'merchant_id'  => $merchant->id,
            ]);
        } catch (\Exception $e) {
            try {
                $customer = app('customer_service')->registerCustomerByEcommerceId($merchant->id, $customer_id);
            } catch (\Exception $e) {
                return response()->json($responseData, 200);
            }
        }
        if (! isset($customer) || ! $customer || $customer->merchant_id !== $merchant->id) {
            return response()->json($responseData, 200);
        }

        $responseData['auth'] = true;

        return response()->json($responseData, 200);
    }

    public function getCustomer(Request $request)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json(['message' => 'Invalid request data'], 403);
        }

        try {
            $customer = $this->customers->withCriteria([
                new EagerLoad([
                    'orders',
                    'points',
                ]),
                new WithUsedCoupons(),
                new WithEarnedPointsHotFix(),
                new WithEarnedPointsInYear(),
                new WithTierHistory(),
            ])->find($request->get('customer_id'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Customer not found',
                'error'   => $e->getMessage(),
            ], 403);
        }
        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 403);
        }

        $customersAdditionalData = [];
        try {
            $referralSettings = $this->referralSettings->findWhereFirst(['merchant_id' => $customer->merchant_id]);
            $customersAdditionalData['referral_settings'] = $referralSettings;
        } catch (\Exception $e) {
            //
        }

        $active_merchant_rewards = $this->merchantRewards->withCriteria([
            new EagerLoad(['reward']),
        ])->findWhere([
            'merchant_id' => $request->get('merchant_id'),
            'active_flag' => 1,
        ]);

        $customerPointsTransactions = $this->points->withCriteria([
            new SpentPoints()
        ])->findWhere([
            'customer_id' => $request->get('customer_id'),
        ]);

        foreach ($active_merchant_rewards as $active_merchant_reward) {

            $is_limit_reached = 0;

            if ($active_merchant_reward->spending_limit) {
                $spendingLimitValue = (int)$active_merchant_reward->spending_limit_value;
                $spendingLimitPeriod = $active_merchant_reward->spending_limit_period;

                $toDateTimeTS = null;
                switch ($spendingLimitPeriod) {
                    case 'lifetime':
                        break;
                    case 'month':
                        $toDateTimeTS = Carbon::now()->subMonth()->timestamp;
                        break;
                    case 'week':
                        $toDateTimeTS = Carbon::now()->subWeek()->timestamp;
                        break;
                }

                if ($toDateTimeTS) {
                    $relatedPointsTransactions = $customerPointsTransactions->filter(function ($item) use (
                        $toDateTimeTS,
                        $active_merchant_reward
                    ) {
                        return $item->merchant_reward_id === $active_merchant_reward->id && $item->created_at->timestamp > $toDateTimeTS;
                    });
                } else {
                    $relatedPointsTransactions = $customerPointsTransactions->filter(function ($item) use (
                        $active_merchant_reward
                    ) {
                        return $item->merchant_reward_id === $active_merchant_reward->id;
                    });
                }

                $countRewardIssued = $relatedPointsTransactions->count();
                if ($countRewardIssued >= $spendingLimitValue) {
                    $is_limit_reached = 1;
                }

            }

            $customersAdditionalData['rewards_spending_limits'][] = [
                'id' => $active_merchant_reward->id,
                'is_limit_reached' => $is_limit_reached
            ];

            $customersAdditionalData['reward_coupons'][] = [
                'id' => $active_merchant_reward->id,
                'is_available' => ($this->rewardCoupon->countAvailableByMerchantRewardId($active_merchant_reward->id) > 0),
            ];
        }

        return fractal($customer)->transformWith(new CustomerTransformer($customersAdditionalData))->toArray();
    }

    public function getCustomerPointsActivity(Request $request)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json(['message' => 'Invalid request data'], 403);
        }

        $points = $this->points->withCriteria([
            new ByCustomer($request->get('customer_id')),
            new LatestFirst(),
        ])->paginate(4);

        return fractal($points->getCollection())
            ->transformWith(new PointTransformer)
            ->paginateWith(new IlluminatePaginatorAdapter($points))
            ->toArray();
    }

    public function getCustomerRewards(Request $request)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json(['message' => 'Invalid request data'], 403);
        }

        $coupons = $this->coupons->withCriteria([
            new WithTrashedMerchantRewards(),
            new LatestFirst(),
        ])->findWhere([
            'customer_id' => $request->get('customer_id'),
            'is_used'     => 0,
        ]);

        return fractal($coupons)->transformWith(new CouponTransformer)->toArray();
    }

    public function getCustomerActions(Request $request)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json([], 200);
        }

        $active_merchant_actions = $this->merchantActions->withCriteria([
            new EagerLoad(['action']),
        ])->findWhere([
            'merchant_id' => $request->get('merchant_id'),
            'active_flag' => 1,
        ]);

        $customerPointsTransactions = $this->points->withCriteria([
            new EarnedPoints(),
            new LatestFirst(),
        ])->findWhere([
            'customer_id' => $request->get('customer_id'),
        ]);


        for ( $i = 0; $i < count( $active_merchant_actions ); $i++ ) {

            // Checking action completion
            $active_merchant_actions[$i]->completed = $this->checkActionCompletion( $active_merchant_actions[$i], $customerPointsTransactions );

            // Recount points per $ [if "make a purchase" action]
            if( $active_merchant_actions[$i]->action->url == 'make-a-purchase' ) {


                $customer = $this->customers->find( $request->get('customer_id') );
                if( $customer && $customer->tier ) {

                    $multiplier = $customer->tier->multiplier ?? 1;

                    $merchant = $this->merchants->withCriteria([
                        new EagerLoad(['detail']),
                    ])->find($request->get('merchant_id'));

                    $pointSettings = $merchant->points_settings;
                    if (! $pointSettings) {
                        $pointSettings = $this->pointSettings->getDefaults();
                    }

                    $reward_text_array = explode( ' ', $active_merchant_actions[$i]['reward_text'] );
                    $reward_text_array[0] = $active_merchant_actions[$i]['point_value'] * $multiplier;
                    $reward_text_array[1] = $pointSettings->plural_name;
                    $active_merchant_actions[$i]['point_value'] = $reward_text_array[0];
                    $point_reward_string = implode( ' ', $reward_text_array );
                    $active_merchant_actions[$i]['reward_text'] = $point_reward_string;
                }
            }
        }

        return fractal($active_merchant_actions)->transformWith(new CustomerMerchantActionTransformer)->toArray();
    }

    private function checkActionCompletion( $action, $customerPointsTransactions ) {

        $completed = 0;

        if ( $action->earning_limit ) {
            // Has limits
            $earningLimitValue = intval( $action->earning_limit_value );
            $earningLimitType = $action->earning_limit_type;
            $earningLimitPeriod = $action->earning_limit_period;

            $toDateTimeTS = null;
            switch ($earningLimitPeriod) {
                case 'lifetime':
                    break;
                case 'year':
                    $toDateTimeTS = Carbon::now()->subYear()->timestamp;
                    break;
                case 'month':
                    $toDateTimeTS = Carbon::now()->subMonth()->timestamp;
                    break;
                case 'week':
                    $toDateTimeTS = Carbon::now()->subWeek()->timestamp;
                    break;
            }

            if ($toDateTimeTS) {
                $relatedPointsTransactions = $customerPointsTransactions->filter(function ($item) use (
                    $toDateTimeTS,
                    $action
                ) {
                    return $item->merchant_action_id == $action->id && $item->created_at->timestamp > $toDateTimeTS;
                });
            } else {
                $relatedPointsTransactions = $customerPointsTransactions->filter(function ($item) use (
                    $action
                ) {
                    return $item->merchant_action_id == $action->id;
                });
            }

            switch ($earningLimitType) {
                case 'times':
                    $validPointsCount = 0;
                    $rolledBackPointsCount = 0;
                    foreach ($relatedPointsTransactions as $pointsTransaction) {
                        if ($pointsTransaction->rollback) {
                            $rolledBackPointsCount++;
                        } else {
                            $validPointsCount++;
                        }
                    }
                    if (($validPointsCount - $rolledBackPointsCount) >= $earningLimitValue) {
                        $completed = 1;
                    }
                    break;
                case 'points':
                    $sumEarnedPoints = $relatedPointsTransactions->sum('point_value');
                    if ($sumEarnedPoints >= $earningLimitValue) {
                        $completed = 1;
                    }
                    break;
            }
        }
        return $completed;
    }

    public function findCustomerReward(Request $request, $id)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json(['message' => 'Invalid request data'], 403);
        }

        $coupon = $this->coupons->withCriteria([
            new WithTrashedMerchantRewards(),
            new LatestFirst(),
        ])->findWhereFirst([
            'customer_id' => $request->get('customer_id'),
            'id'          => $id,
        ]);

        return fractal($coupon)->transformWith(new CouponTransformer)->toArray();
    }

    public function updateBirthday(CustomerUpdateRequest $request)
    {
        $data = $request->only(['birthday']);
        if (isset($data['birthday']) && trim($data['birthday'])) {
            $data['birthday'] = Carbon::createFromFormat('m/d/Y', $data['birthday'])->format('Y-m-d');
        }

        try {
            $updated = $this->customers->withCriteria([
                new ByMerchant($request->get('merchant_id')),
            ])->update($request->get('customer_id'), $data);

            if ($updated) {
                return response()->json([], 204);
            } else {
                return response()->json(['Something went wrong'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 404);
        }
    }

    public function getCustomerReferralActivity(Request $request)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json(['message' => 'Invalid request data'], 403);
        }

        $referralClicks = $this->referralRepo->findWhere([
            'referral_customer_id' => $request->get('customer_id'),
        ])->count();

        $referralPurchases = $this->orders->findWhere([
            'referring_customer_id' => $request->get('customer_id'),
        ])->count();

        return response()->json([
            'data' => [
                'clicks'    => $referralClicks,
                'purchases' => $referralPurchases,
            ],
        ], 200);
    }

    public function incrementShares(Request $request)
    {
        if (! $request->get('customer_id') || ! trim($request->get('customer_id'))) {
            return response()->json(['message' => 'Invalid request data'], 403);
        }

        app('customer_service')->incrementReferralShareCounter($request->get('customer_id'), $request->all());

        return response()->json([], 204);
    }
}
