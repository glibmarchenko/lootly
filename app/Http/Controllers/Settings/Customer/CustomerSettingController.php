<?php

namespace App\Http\Controllers\Settings\Customer;

use App\Http\Controllers\Api\Widget\WidgetRewardController;
use App\Models\Customer;
use App\Models\Tier;
use App\Models\TierHistory;
use App\Repositories\Contracts\CustomerTransactionFlagRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\ReferralSettingsRepository;
use App\Repositories\Contracts\TierHistoryRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\Limit;
use App\Repositories\Eloquent\Criteria\Offset;
use App\Repositories\Eloquent\Criteria\ByTier;
use App\Repositories\Eloquent\Criteria\OrderBy;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\SearchByAll;
use App\Repositories\Eloquent\Criteria\BetweenDates;
use App\Repositories\Eloquent\Criteria\SharedLock;
use App\Repositories\Eloquent\Criteria\UpdateLock;
use App\Repositories\Eloquent\Criteria\WithTierName;
use App\Repositories\Eloquent\Criteria\SumByRelation;
use App\Repositories\Eloquent\Criteria\CountByRelation;
use App\Repositories\Eloquent\Criteria\WithEarnedPoints;
use App\Repositories\Eloquent\Criteria\WithEarnedPointsInYear;
use App\Repositories\Eloquent\Criteria\WithUsedCoupons;

use App\Transformers\CouponTransformer;
use App\Transformers\CustomerTransformer;
use App\Transformers\PointTransformer;
use App\Transformers\TagTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Customer\CustomerUpdateRequest;
use App\Repositories\CustomerRepository;
use App\Repositories\MerchantRepository;

class CustomerSettingController extends Controller
{
    protected $merchantRepo;

    protected $customerRepo; // @todo: Refactor and delete this

    protected $currentMerchant;

    protected $customers;

    protected $referralSettings;

    protected $customerTransactionFlags;

    protected $merchantRewards;

    protected $points;

    private $tierHistory;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        CustomerRepository $customerRepository,
        MerchantRepository $merchantRepository,
        \App\Repositories\Contracts\CustomerRepository $customers,
        ReferralSettingsRepository $referralSettings,
        CustomerTransactionFlagRepository $customerTransactionFlags,
        MerchantRewardRepository $merchantRewards,
        PointRepository $points,
        TierHistoryRepository $tierHistory
    ) {
        $this->customers = $customers;
        $this->referralSettings = $referralSettings;
        $this->customerTransactionFlags = $customerTransactionFlags;
        $this->merchantRewards = $merchantRewards;
        $this->points = $points;
        $this->tierHistory = $tierHistory;

        $this->merchantRepo = $merchantRepository;
        $this->customerRepo = $customerRepository;

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->currentMerchant = $this->merchantRepo->getCurrent();
            if (! $this->currentMerchant) {
                abort(401, 'Please add account (merchant)');
            }

            return $next($request);
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $criteries = [
            new EagerLoad([
                'points',
                'tier_history',
                'coupons',
            ]),
            new ByMerchant($this->currentMerchant->id),
            new WithEarnedPointsInYear(),
            new CountByRelation('orders', 'purchases'),
            new SumByRelation('orders', 'total_price', 'total_spend'),
            new WithEarnedPoints(),
            new WithTierName(),
            new Limit($request->get('limit')),
            new Offset($request->get('offset')),
        ];
        $criteriesForCount = [
            new ByMerchant($this->currentMerchant->id),
        ];

        $sortBy = $request->get('sort_by');
        $sortDir = $request->get('sort_dir');

        if (!empty($sortBy) && !empty($sortDir)) {
            $criteries[] = new OrderBy($sortBy, $sortDir);
        }

        $search = $request->get('search');
        $columnsToSearch = [
            'name',
            'tier.name',
            'email'
        ];
        if (!empty($search)) {
            if (strlen($search) > 2) {
                $criteries[] = new SearchByAll($search, $columnsToSearch);
                $criteriesForCount[] = new SearchByAll($search, $columnsToSearch);
            }
        }

        $tier = $request->get('tier');

        if (!empty($tier) && $tier != 'All' && empty($search)) {
            $criteries[] = new ByTier($tier);
            $criteriesForCount[] = new ByTier($tier);
        }

        $start = $request->get('start');
        $end = $request->get('end');

        if (!empty($start) && !empty($end) && empty($search)) {
            $criteries[] = new BetweenDates($start, $end);
            $criteriesForCount[] = new BetweenDates($start, $end);
        }

        $customers = $this->customers->withCriteria($criteries)->all();
        $this->customers->clearEntity();
        $customersCount = $this->customers->withCriteria($criteriesForCount)->count();

        if ($customersCount == 0) {
            return response()->json(['status' => \Config('customers.strings.noCustomersAtAll')]);
        }

        if (count($customers) == 0) {
            return response()->json(['status' => \Config('customers.strings.noCustomersOnTimerange')]);
        }

        $customersAdditionalData = [];
        try {
            $referralSettings = $this->referralSettings->findWhereFirst(['merchant_id' => $this->currentMerchant->id]);
            $customersAdditionalData['referral_settings'] = $referralSettings;
        } catch (\Exception $e) {
            //
        }

        return response()->json([
            'customers' => fractal($customers)
                ->transformWith(new CustomerTransformer($customersAdditionalData))
                ->toArray()['data'],
            'total'     => $customersCount,
        ]);
    }

    public function update(CustomerUpdateRequest $request, $customer_id)
    {
        $data = $request->only(['birthday']);
        if (isset($data['birthday']) && trim($data['birthday'])) {
            $data['birthday'] = Carbon::createFromFormat('m/d/Y', $data['birthday'])->format('Y-m-d');
        }

        try {
            $updated = $this->customers->withCriteria([
                new ByMerchant($this->currentMerchant->id),
            ])->update($customer_id, $data);

            if ($updated) {
                return response()->json([], 204);
            } else {
                return response()->json(['Something went wrong'], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 404);
        }
    }

    public function updateTier(Request $request, int $customerId)
    {
        $request->validate([
            'tier_id' => 'required|integer|exists:tiers,id',
        ]);

        $customer = Customer::where([
            'id' => $customerId,
            'merchant_id' => $this->currentMerchant->id
        ])->first();

        if (! $customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $tier = Tier::where([
            'id' => $request->input('tier_id'),
            'merchant_id' => $this->currentMerchant->id
        ])->first();

        if (! $tier) {
            return response()->json(['message' => 'Tier not found'], 404);
        }

        $this->tierHistory->create([
            'customer_id' => $customer->id,
            'new_tier_id' => $tier->id,
            'old_tier_id' => $customer->tier_id,
            'activity' => TierHistory::ACTIVITY_USER_CHANGES,
        ]);

        $updated = $this->customerRepo->updateTier($customer->id, $tier->id);

        if ($updated) {
            return response()->json(['message' => "{$tier->name} successfully assigned"], 200);

        } else {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function find($customer_id)
    {
        try {
            $customer = $this->customers->withCriteria([
                new EagerLoad([
                    'tier',
                    'orders',
                    'points',
                ]),
                new WithUsedCoupons(),
                new WithEarnedPoints(),
                new WithEarnedPointsInYear(),
                //new WithTierHistory(),
                new LatestFirst(),
            ])->find($customer_id);
        } catch (\Exception $e) {
            return response()->json([], 404);
        }

        if (! $customer || $customer->merchant_id != $this->currentMerchant->id) {
            return response()->json([], 404);
        }

        $customersAdditionalData = [];
        try {
            $referralSettings = $this->referralSettings->findWhereFirst(['merchant_id' => $this->currentMerchant->id]);
            $customersAdditionalData['referral_settings'] = $referralSettings;
        } catch (\Exception $e) {
            //
        }

        return fractal($customer)
            ->parseIncludes(['tier'])
            ->transformWith(new CustomerTransformer($customersAdditionalData))
            ->toArray();
        /*return response()->json([
            'customer' => $customer,
        ]);*/
    }

    public function getTags($customer_id)
    {
        $customerTags = $this->customerRepo->getTags($this->currentMerchant, $customer_id);

        if (count($customerTags)) {
            return fractal()
                ->collection($customerTags)
                ->parseIncludes([])
                ->transformWith(new TagTransformer)
                ->toArray();
        } else {
            return response()->json([
                'data' => [],
            ]);
        }
    }

    public function storeTags(Request $request, $customer_id)
    {
        $newTagsList = $request->get('tags');
        $merchantTags = $this->merchantRepo->getTags($this->currentMerchant);
        $customerTags = $this->customerRepo->getTags($this->currentMerchant, $customer_id);

        $customerTagsList = $customerTags->map(function ($item) {
            return $item->name;
        })->toArray();

        $merchantTagsList = $merchantTags->map(function ($item) {
            return $item->name;
        })->toArray();

        $removeTags = array_diff($customerTagsList, $newTagsList);
        $removeTagIDs = $merchantTags->filter(function ($item) use ($removeTags) {
            return in_array($item->name, $removeTags);
        })->pluck('id')->toArray();

        $remove = $this->customerRepo->removeTags($this->currentMerchant, $customer_id, $removeTagIDs);

        $createTags = array_diff($newTagsList, $merchantTagsList);

        $createdTags = $this->merchantRepo->createTags($this->currentMerchant, $createTags);
        $createdTagIDs = [];
        for ($i = 0; $i < count($createdTags); $i++) {
            $createdTagIDs[] = $createdTags[$i]->id;
        };

        $addTags = array_diff($newTagsList, $customerTagsList);
        $addTagIDs = $merchantTags->filter(function ($item) use ($addTags) {
            return in_array($item->name, $addTags);
        })->pluck('id')->toArray();

        $addTagIDs = array_unique(array_merge($addTagIDs, $createdTagIDs));

        $this->customerRepo->storeTags($this->currentMerchant, $customer_id, $addTagIDs);
    }

    public function earning($customer_id)
    {
        $customerEarning = $this->customerRepo->getEarnedPoints($this->currentMerchant, $customer_id);

        $customerEarning = $customerEarning->map(function ($item) {
            $item->created_at_formatted = $item->created_at->format('Y-m-d\TH:i:sP');

            return $item;
        });

        if ($customerEarning) {
            return fractal()
                ->collection($customerEarning)
                ->parseIncludes(['action'])
                ->transformWith(new PointTransformer)
                ->toArray();
            /*return response()->json([
                'data' => $customerEarning->toArray()
            ]);*/
        } else {
            return response()->json([
                'data' => [],
            ]);
        }
    }

    public function spending($customer_id)
    {
        $customerSpending = $this->customerRepo->getSpentPoints($this->currentMerchant, $customer_id);

        if ($customerSpending) {
            return fractal()->collection($customerSpending)->parseIncludes([
                'reward',
                'coupon',
            ])->transformWith(new PointTransformer)->toArray();
            /*return response()->json([
                'data' => $customerSpending->toArray()
            ]);*/
        } else {
            return response()->json([
                'data' => [],
            ]);
        }
    }

    public function vipActivity($customer_id)
    {
        $customerVipActivity = $this->customerRepo->getVipActivity($this->currentMerchant, $customer_id);

        if ($customerVipActivity) {
            return response()->json([
                'data' => $customerVipActivity->toArray(),
            ]);
        } else {
            return response()->json([
                'data' => [],
            ]);
        }
    }

    public function orders($customer_id)
    {
        $customerOrders = $this->customerRepo->getOrders($this->currentMerchant, $customer_id);

        if ($customerOrders) {
            return response()->json([
                'data' => $customerOrders->toArray(),
            ]);
        } else {
            return response()->json([
                'data' => [],
            ]);
        }
    }

    public function referralOrders($customer_id)
    {
        $customerOrders = $this->customerRepo->getReferralOrders($this->currentMerchant, $customer_id);

        if ($customerOrders) {
            return response()->json([
                'data' => $customerOrders->toArray(),
            ]);
        } else {
            return response()->json([
                'data' => [],
            ]);
        }
    }

    public function giveReward(Request $request, Customer $customer)
    {
        if (! $this->currentMerchant) {
            abort(401);
        }

        if ($customer->merchant_id !== $this->currentMerchant->id) {
            abort(400);
        }

        $merchantRewards = $this->currentMerchant->rewards()->pluck('id')->toArray();

        $request->validate([
            'reward'       => 'required|in:'.implode(',', $merchantRewards),
            'deductPoints' => 'required|boolean',
        ]);

        $data = $request->all();

        $spend_points_record = null;

        $rewardId = $request->get('reward');

        try {
            $active_merchant_reward = $this->merchantRewards->withCriteria([
                new EagerLoad(['reward']),
            ])->findWhereFirst([
                'id'          => $rewardId,
                'merchant_id' => $customer->merchant_id,
                'active_flag' => 1,
            ]);

            if($request->get('deductPoints')) {
                $this->customers->transaction(function () use ($request, $customer, $rewardId, $active_merchant_reward, &$spend_points_record) {

                    $lockTransaction = $this->customerTransactionFlags->withCriteria([
                        new UpdateLock(),
                    ])->updateOrCreate(['customer_id' => $customer->id], ['locked' => 1]);

                    $points = $this->points->withCriteria([
                        new ByCustomer($customer->id),
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
                        'merchant_id'        => $customer->merchant_id,
                        'customer_id'        => $customer->id,
                        'point_value'        => $points_required * -1,
                        'merchant_reward_id' => $active_merchant_reward->id,
                        'title'              => $active_merchant_reward->reward_name,
                        'type'               => $active_merchant_reward->reward_type,
                    ]);

                    // Unlocking transaction
                    $this->customerTransactionFlags->clearEntity();
                    $this->customerTransactionFlags->update($lockTransaction->id, ['locked' => 0]);
                });
            }
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Cannot perform point deducting transaction',
                'error'   => $exception->getMessage(),
            ], 405);
        }


        // Generate coupon
        try {
            $spend_points_record_id = null;
            if (isset($spend_points_record) && $spend_points_record) {
                $spend_points_record_id = $spend_points_record->id;
            }
            $newCoupon = app('coupon_service')->generateRewardCoupon($rewardId, $customer->id, $spend_points_record_id, null, ['available_for_owner_only' => true]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Cannot generate coupon for chosen reward',
                'error'   => $exception->getMessage(),
            ], 405);
        }

        if (isset($newCoupon) && $newCoupon) {
            // Update point record
            if (isset($spend_points_record_id) && $spend_points_record_id) {
                $this->points->clearEntity();
                try {
                    $this->points->update($spend_points_record_id, [
                        'coupon_id' => $newCoupon->id,
                    ]);
                } catch (\Exception $e) {
                    // Hmm.. something wrong
                }
            }

            return fractal($newCoupon)->transformWith(new CouponTransformer())->toArray();
        } else {
            return response()->json([
                'message' => 'Coupon for chosen reward was not generated successfully',
            ], 405);
        }

        //$newReward = $this->customerRepo->addReward($customer, $data);
        //
        //if (! $newReward) {
        //
        //}
        //

    }

    public function adjustPoints(Request $request, Customer $customer)
    {
        if ($customer->merchant_id !== $this->currentMerchant->id) {
            abort(401);
        }

        $request->validate([
            'amount' => 'required|integer',
            'reason' => 'required|max:191',
        ]);

        $data = $request->all();

        $amount = (int) $data['amount'];
        $reason = trim($data['reason']) ?: 'Manual Adjustment';

        if (! $amount) {
            return response()->json([
                'data' => [],
            ], 201);
        }

        $points = $this->customerRepo->addCustomPoints($customer, $amount, $reason);
        if (! $points) {
            return response()->json([
                'data' => [],
            ], 201);
        }

        return fractal()->item($points)->transformWith(new PointTransformer)->toArray();
    }
}
