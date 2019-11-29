<?php

namespace App\Repositories;

use App\Merchant;
use App\Models\Customer;
use App\Models\CustomerTag;
use App\Models\PaidPermission;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\HasActionWhere;
use App\Repositories\Eloquent\EloquentMerchantActionRepository;
use App\Repositories\Eloquent\EloquentPointRepository;
use App\Repositories\Eloquent\EloquentReferralSettingsRepository;
use App\Transformers\TagTransformer;
use App\Transformers\MerchantTransformer;
use App\Transformers\VIPProfileTransformer;
use App\Transformers\PointProfileTransformer;
use App\Transformers\OrdersProfileTransformer;
use App\Transformers\MerchantRewardTransformer;
use App\Transformers\CustomerProfileTransformer;
use App\Transformers\MerchantDetailsTransformer;
use App\Transformers\ReferralOrdersProfileTransformer;
use Carbon\Carbon;
use App\Contracts\Repositories\CustomerRepository as CustomerRepositoryContract;

class CustomerRepository implements CustomerRepositoryContract
{
    public $baseQuery;

    protected $merchantRepo;
    protected $referralSettings;
    protected $merchantActions;

    // cache indexes
    private $findIndex = 'customerFind';
    private $tagsIndex = 'customerTags';
    private $earnedPointsIndex = 'customerEarnedPoints';
    private $spentPointsIndex = 'customerSpentPoints';

    //protected $pointRepo;

    public function __construct()
    {
        $this->baseQuery = Customer::query();
        $this->merchantRewardRepo = new MerchantRewardRepository;
        $this->merchantRepo = new MerchantRepository();
        $this->tagRepo = new TagRepository();
        $this->referralSettings = new EloquentReferralSettingsRepository();
        //$this->pointRepo = $pointRepository;
        $this->merchantActions = new EloquentMerchantActionRepository();
    }

    public function find($id)
    {
        // return CacheRepository::rememberCacheByTag( Customer::class . $id , \get_class($this) . '@find', function() use ($id){
            return $this->baseQuery->with([
                'tier',
                'coupons'               => function ($q) {
                    $q->where('coupons.is_used', 1);
                },
                'orders',
                'points',
                'earned_points'         => function ($q) {
                    $q->where(function ($q1) {
                        $q1->where('point_value', '>=', 0);
                        $q1->where('rollback', 0);
                    });
                    $q->orWhere(function ($q1) {
                        $q1->where('point_value', '<', 0);
                        $q1->where('rollback', 1);
                    });
                },
                'earned_points_in_year' => function ($q) {
                    $q->where(function ($q1) {
                        $q1->where(function ($q2) {
                            $q2->where('point_value', '>=', 0);
                            $q2->where('rollback', 0);
                        });
                        $q1->orWhere(function ($q2) {
                            $q2->where('point_value', '<', 0);
                            $q2->where('rollback', 1);
                        });
                    });
                    $q->where('created_at', '>=', Carbon::now()->subYear());
                },
            ])->where('customers.id', $id)->first();
        // });
    }

    /**
     * @param $data
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function create($data, $merchantObj)
    {
        $customer = $this->baseQuery->create([
            'merchant_id'   => $merchantObj->id,
            'name'          => $data['name'],
            'email'         => $data['email'],
            'referral_slug' => uniqid("loot"),
        ]);

        return $customer;
    }

    public function updateOrCreate(Merchant $merchant, array $data)
    {
        if (! isset($data['email']) || ! trim($data['email'])) {
            return null;
        }
        $customer = Customer::updateOrCreate([
            'merchant_id' => $merchant->id,
            'email'       => $data['email'],
        ], $data);

        if ($customer->wasRecentlyCreated) {
            $customer->referral_slug = uniqid("loot");
            $customer->save();
        }

        return $customer;
    }

    public function getByEmail($email)
    {

        return Customer::query()->where('email', '=', $email)->first();
    }

    public function update($customer_id, $merchantObj, $data)
    {
        $customer = $this->baseQuery->where('customers.id', '=', $customer_id)
            ->where('customers.merchant_id', '=', $merchantObj->id)
            ->first();

        if (! $customer) {
            return;
        }

        if (isset($data['birthday']) && trim($data['birthday'])) {
            $data['birthday'] = Carbon::createFromFormat('m/d/Y', $data['birthday'])->format('Y-m-d');
        }
        $customer->update($data);

        return $customer;
    }

    public function delete($user)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @param $emailcustomers
     *
     * @return bool|\Illuminate\Database\Eloquent\Model|null|object|static
     */
    public function hasCustomer($email)
    {
        $customer = $this->baseQuery->where('email', '=', $email)->first();
        if ($customer) {
            return $customer;
        } else {
            return false;
        }
    }

    public function getPoints($merchantObj)
    {
        if (! $merchantObj) {
            return null;
        }
        $customer = $this->baseQuery->Join('points', 'points.customer_id', '=', 'customers.id')
            ->Join('merchant_actions', 'points.merchant_action_id', '=', 'merchant_actions.id')
            ->where('customers.merchant_id', '=', $merchantObj->id)
            ->select('customers.name', 'merchant_actions.action_name', 'points.point_value', 'points.created_at')
            ->get();

        return $customer;
    }

    public function getTags($merchantObj, $customerId)
    {
        // return CacheRepository::rememberCacheByTag(Customer::class . $customerId, \get_class($this) . '@getTags', function () use($customerId, $merchantObj){
            $customer = Customer::where('customers.id', '=', $customerId)
                ->where('customers.merchant_id', '=', $merchantObj->id)
                ->first();

            if ($customer) {
                return $customer->tags()->get();
            } else {
                return collect([]);
            }
        // });
    }

    public function removeTags($merchantObj, $customerId, $tags = [])
    {
        $customer = Customer::where('customers.id', '=', $customerId)
            ->where('customers.merchant_id', '=', $merchantObj->id)
            ->first();

        if ($customer) {
            return $customer->tags_pivot()->whereIn('customer_tags.id', $tags)->delete();
        }
    }

    public function storeTags($merchantObj, $customerId, $tags = [])
    {
        $customer = Customer::where('customers.id', '=', $customerId)
            ->where('customers.merchant_id', '=', $merchantObj->id)
            ->first();

        if ($customer) {
            $tagPivot = [];
            foreach ($tags as $tagID) {
                $tagPivot[] = new CustomerTag(['tag_id' => $tagID]);
            }

            return $customer->tags_pivot()->saveMany($tagPivot);
        }
    }

    public function getEarnedPoints($merchantObj, $customerId)
    {
        if (! $merchantObj) {
            return null;
        }
        // return CacheRepository::rememberCacheByTag(Customer::class . $customerId, \get_class($this) . '@getEarnedPoints', function() use($customerId, $merchantObj){
            $customer = Customer::where([
                'customers.id'          => $customerId,
                'customers.merchant_id' => $merchantObj->id,
            ])->first();
    
            if ($customer) {
                $earning = $customer->points()
                    ->where('points.point_value', '>=', 0)
                    ->orderBy('points.created_at', 'desc')
                    ->get();
    
                return $earning;
            }
            return collect([]);
        // });

    }

    public function getSpentPoints($merchantObj, $customerId)
    {
        if (! $merchantObj) {
            return null;
        }
        // return CacheRepository::rememberCacheByTag(Customer::class . $customerId, \get_class($this) . '@getSpentPoints', function() use($customerId, $merchantObj){
            $customer = Customer::where([
                'customers.id'          => $customerId,
                'customers.merchant_id' => $merchantObj->id,
            ])->first();

            if ($customer) {
                $spending = $customer->points()
                    ->where('points.point_value', '<', 0)
                    ->orderBy('points.created_at', 'desc')
                    ->get();

                return $spending;
            }

            return collect([]);
        // });
    }

    public function getOrders($merchantObj, $customerId)
    {
        if (! $merchantObj) {
            return null;
        }
        $customer = Customer::where([
            'customers.id'          => $customerId,
            'customers.merchant_id' => $merchantObj->id,
        ])->first();

        if ($customer) {
            $orders = $customer->orders()->latest()->with(['coupon'])->get();

            return $orders;
        }

        return collect([]);
    }

    public function getReferralOrders($merchantObj, $customerId)
    {
        if (! $merchantObj) {
            return null;
        }
        $customer = Customer::where([
            'customers.id'          => $customerId,
            'customers.merchant_id' => $merchantObj->id,
        ])->first();

        if ($customer) {
            $orders = $customer->referral_orders()->with(['customer'])->get();

            return $orders;
        }

        return collect([]);
    }

    /**
     * @param $merchantObj
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function get($merchantObj)
    {

        $customer = $this->baseQuery->with([
            'tier',
            'orders',
            'points',
            'earned_points'         => function ($q) {
                $q->where(function ($q1) {
                    $q1->where('point_value', '>=', 0);
                    $q1->where('rollback', 0);
                });
                $q->orWhere(function ($q1) {
                    $q1->where('point_value', '<', 0);
                    $q1->where('rollback', 1);
                });
            },
            'earned_points_in_year' => function ($q) {
                $q->where(function ($q1) {
                    $q1->where(function ($q2) {
                        $q2->where('point_value', '>=', 0);
                        $q2->where('rollback', 0);
                    });
                    $q1->orWhere(function ($q2) {
                        $q2->where('point_value', '<', 0);
                        $q2->where('rollback', 1);
                    });
                });
                $q->where('created_at', '>=', Carbon::now()->subYear());
            },
        ])
            ->where('customers.merchant_id', '=', $merchantObj->id)
            ->orderBy('customers.created_at', 'desc')
            ->orderBy('customers.id', 'desc')
            ->get();

        /*$customer = $this->baseQuery
            ->with('tier')
            ->leftJoin('orders', 'customers.id', '=', 'orders.customer_id')
            ->leftJoin('points', 'orders.id', '=', 'points.order_id')
            ->select('customers.*', 'customers.name', \DB::raw('SUM(points.point_value) as points'), \DB::raw('SUM(orders.total_price) as total_spend'), \DB::raw('COUNT(orders.id) as purchases'))
            ->groupBy('customers.id')
            ->where('customers.merchant_id', '=', $merchantObj->id)
            ->get();*/

        return $customer;
    }

    public function getForExport($merchantObj)
    {
        $customer = $this->baseQuery->with([
            'tier',
            'orders',
            'points',
            'coupons',
            'earned_points' => function ($q) {
                $q->where('point_value', '>=', 0);
            },
        ])->where('customers.merchant_id', '=', $merchantObj->id)->orderBy('created_at');

        return $customer->get();
    }

    /**
     * @param Merchant $merchantObj
     * @param array    $data
     * @param bool     $awardPoints
     */
    public function add(Merchant $merchantObj, array $data, $awardPoints = false)
    {
        foreach ($data as $key => $row) {

            $customersData = [
                'email'  => $row['email'],
                'name'   => $row['name'],
                'points' => $row['points'],
                'title'  => 'Imported',
                'reason' => 'Imported',
            ];

            $customerObj = Customer::firstOrNew([
                'email'       => $customersData['email'],
                'merchant_id' => $merchantObj->id,
            ]);

            $noAccount = false;
            if (!$customerObj->exists) {
                $customerObj->name = $customersData['name'];
                $customerObj->referral_slug = uniqid("loot");
                $customerObj->save();

                $points = new PointRepository(new EloquentPointRepository());
                $points->create($customersData, $customerObj, $customersData['points'], 'Imported');

                $noAccount = true;
            }

            if ($awardPoints && $noAccount) {
                $createAccountAction = $this->merchantActions->withCriteria([
                    new ByMerchant($merchantObj->id),
                    new EagerLoad(['action']),
                    new HasActionWhere([
                        'type' => 'Account',
                        'url'  => 'create-account',
                    ]),
                ])->findWhere([
                    'active_flag' => 1,
                ]);
                $this->merchantActions->clearEntity();

                if (count($createAccountAction)) {
                    app('action_service')->validateAccountCreateActionAndCreditPoints($createAccountAction[0], $customerObj);
                }
            }
        }
    }

    /**
     * @param Merchant|null $merchant
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getTotalVipActivity(Merchant $merchant = null)
    {   
        if(empty($merchantObj)) {
            $merchantRepository = new MerchantRepository();
            $merchantObj = $merchantRepository->getCurrent();
        }
        return $this->baseQuery
            ->where('merchant_id', '=', $merchantObj->id)
            ->with('tier')
            ->get();
    }

    public function getVipActivity($merchantObj, $customerId)
    {
        if (! $merchantObj) {
            return null;
        }
        $customer = Customer::where([
            'customers.id'          => $customerId,
            'customers.merchant_id' => $merchantObj->id,
        ])->first();

        if ($customer) {
            return $customer->tier_history()->with([
                'new_tier',
                'old_tier',
            ])->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();
        }

        return collect([]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getCustomerByDate()
    {
        return $this->baseQuery->leftJoin('points', 'customers.id', '=', 'points.customer_id')
            ->select('customers.*', \DB::raw('SUM(points.point_value) as points'))
            ->groupBy('customers.id')
            ->get();
    }

    /**
     * @param $customerId
     * @param $tierId
     *
     * @return int
     */
    public function updateTier(int $customerId, int $tierId)
    {
        return Customer::where('id', $customerId)->update([
            'tier_id' => $tierId,
        ]);
    }

    public function addReward($customer, $rewardData)
    {
        $customer = $this->baseQuery->where('id', '=', $customer->id)->firstOrFail();
        //
    }

    public function addCustomPoints($customer, $points = 0, $reason = '')
    {
        $customer = $this->baseQuery->where('id', '=', $customer->id)->firstOrFail();

        return $customer->points()->create([
            'point_value' => $points,
            'reason'      => $reason,
            'title'       => "Admin",
            'type'        => 'Admin',
            'merchant_id' => $customer->merchant_id,
        ]);
    }

    /**
     * @param App\Merchant $merchant
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return Collection[App\Models\TierHistory]
     */
    public function getVipMembers(Merchant $merchant, \DateTime $startDate = null, \DateTime $endDate = null)
    {
        if (!$merchant) {
            return null;
        }

        $membersQuery = $this->baseQuery
            ->where('merchant_id', '=', $merchant->id)
            ->whereNotNull('tier_id')
            ->with('tier')
            ->with('points')
            ->with('orders');

        if (! empty($startDate) && ! empty($endDate)) {
            $membersQuery = $membersQuery
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where([
                        ['created_at', '>', $startDate],
                        ['created_at', '<', $endDate],
                    ])->orWhere([
                        ['updated_at', '>', $startDate],
                        ['updated_at', '<', $endDate],
                    ]);
                });
        }

        return $membersQuery->get();

        //     $members = $members->filter(function($member) use($startDate, $endDate){
        //         $lastOrder = $member->getLastOrdered();
        //         if($lastOrder->created_at > $startDate && $lastOrder->created_at < $endDate){
        //             return true;
        //         }
        //         return false;
        //     });
        // }
        // return $members;
    }
    /*
    public function findByEcommerceId($ecommerce_id)
    {
        return Customer::where('ecommerce_id', $ecommerce_id)->first();
    }
    */

    /**
     * @param App\Merchant
     * @param int id of customer
     * @return array
     */
    public function getProfileData(Merchant $merchant, int $customerId){
        if(!isset($merchant)){
            return null;
        }
        $company_logo = env('DefaultCompanyLogo');
        if (isset($merchant->email_notification_settings)) {
            $company_logo = $merchant->email_notification_settings->company_logo;
        }
        $data['company'] = $merchant->name;
        $data['company_logo'] = $merchant->logo_url ? $merchant->logo_url : $company_logo;

        $data['have_rest_permissions'] = $merchant->checkPermitionByTypeCode(\Config::get('permissions.typecode.CustomerSegmentation'));
        $data['restrictions_upsell'] = PaidPermission::getByTypeCode(\Config::get('permissions.typecode.CustomerSegmentation'));
        $data['woocommerce'] = $merchant->integrations->filter(function ($integration, $key) {
            return $integration->slug === 'woocommerce';
        }) ? true : false;

        $data['merchant_data'] = fractal()
            ->item($merchant)
            ->parseIncludes(['merchant_currency'])
            ->transformWith(new MerchantTransformer)
            ->toArray()['data'];

        try {
            $customer = $this->find($customerId);

            $customersAdditionalData = [];
            try {
                $referralSettings = $this->referralSettings->findWhereFirst(['merchant_id' => $customer->merchant_id]);
                $customersAdditionalData['referral_settings'] = $referralSettings;
            } catch (\Exception $e) {
                //
            }

            $data['overview'] = [];
            if(!empty($customer)){
                $data['overview'] = fractal($customer)
                    ->transformWith(new CustomerProfileTransformer($customersAdditionalData))
                    ->toArray()['data'];
            }
        } catch (\Exception $e) {
            \Log::debug($e);
        }
        $details = $merchant->detail()->first();
        $data['merchant_details'] = [];
        if(!empty($details)){
            $data['merchant_details'] = fractal()
                ->item($details)
                ->transformWith(new MerchantDetailsTransformer)->toArray();
        }
        $data['tags_options'] = fractal()
            ->collection($this->tagRepo->all($merchant))
            ->transformWith(new TagTransformer)->toArray()['data'];

        $data['customer_tags'] = fractal()
            ->collection($this->getTags($merchant, $customerId))
            ->transformWith(new TagTransformer)->toArray()['data'];

        $earnedPoints = $this->getEarnedPoints($merchant, $customerId);
        $data['earning'] = 'Customer has not earned any points yet.';
        if (count($earnedPoints) != 0) {
            $data['earning'] = fractal()
                ->collection($earnedPoints)
                ->parseIncludes(['action'])
                ->transformWith(new PointProfileTransformer)
                ->toArray()['data'];
        }

        $customerSpending = $this->getSpentPoints($merchant, $customerId);
        $data['spending'] = 'Customer has not spent any points yet.';
        if (count($customerSpending) != 0) {
            $data['spending'] = fractal()->collection($customerSpending)->parseIncludes([
                'reward',
                'coupon',
            ])->transformWith(new PointProfileTransformer)
            ->toArray()['data'];
        }

        $customerVipActivity = $this->getVipActivity($merchant, $customerId);
        $data['vip_activity'] = 'Customer has never been apart of a VIP tier.';
        if (count($customerVipActivity) != 0) {
            $data['vip_activity'] = fractal()
                ->collection($customerVipActivity)
                ->parseIncludes(['new_tier', 'old_tier'])
                ->transformWith(new VIPProfileTransformer)
                ->toArray()['data'];
        }

        $customerOrders = $this->getOrders($merchant, $customerId);

        $data['orders'] = 'Customer has not placed an order yet.';
        if (count($customerOrders) != 0) {
            $data['orders'] = fractal()->collection($customerOrders)
                ->transformWith(new OrdersProfileTransformer)
                ->toArray()['data'];
        }

        $referralOrders = $this->getReferralOrders($merchant, $customerId);
        $data['referral_orders'] = 'Customer has not referred anybody yet.';
        if (count($referralOrders) != 0) {
            $data['referral_orders'] = fractal()->collection($referralOrders)
                ->transformWith(new ReferralOrdersProfileTransformer)
                ->toArray()['data'];
        }

        $merchantRewards = $this->merchantRewardRepo->all($merchant);
        $data['rewards'] = [];
        if(count($merchantRewards) != 0){
            $data['rewards'] = fractal()->collection($merchantRewards)
            ->transformWith(new MerchantRewardTransformer)
            ->toArray()['data'];
        }
        // dd($data);
        return $data;
    }
}
