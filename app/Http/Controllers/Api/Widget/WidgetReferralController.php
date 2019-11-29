<?php

namespace App\Http\Controllers\Api\Widget;

use App\Events\ReferralReceiverRewardCouponGenerated;
use App\Events\ReferralShareEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Widget\ReferralEmailRequest;
use App\Models\MerchantReward;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\ReferralRepository;
use App\Repositories\Contracts\ReferralSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\WithReceiverReward;
use App\Transformers\CouponTransformer;
use App\Transformers\ReceiverRewardTransformer;
use Illuminate\Http\Request;

class WidgetReferralController extends Controller
{
    protected $customers;

    protected $merchants;

    protected $coupons;

    protected $referrals;

    protected $referralSettings;

    protected $merchantRewards;

    /**
     * WidgetReferralController constructor.
     *
     * @param \App\Repositories\Contracts\CustomerRepository         $customers
     * @param \App\Repositories\Contracts\MerchantRepository         $merchants
     * @param \App\Repositories\Contracts\CouponRepository           $coupons
     * @param \App\Repositories\Contracts\ReferralRepository         $referrals
     * @param \App\Repositories\Contracts\ReferralSettingsRepository $referralSettings
     * @param \App\Repositories\Contracts\MerchantRewardRepository   $merchantRewards
     */
    public function __construct(
        CustomerRepository $customers,
        MerchantRepository $merchants,
        CouponRepository $coupons,
        ReferralRepository $referrals,
        ReferralSettingsRepository $referralSettings,
        MerchantRewardRepository $merchantRewards
    ) {
        $this->customers = $customers;
        $this->merchants = $merchants;
        $this->coupons = $coupons;
        $this->referrals = $referrals;
        $this->referralSettings = $referralSettings;
        $this->merchantRewards = $merchantRewards;
    }

    public function getReceiverReward(Request $request, $slug)
    {
        try {
            $data = $this->customers->withCriteria([
                new WithReceiverReward(),
            ])->findWhereFirst([
                'referral_slug' => $slug,
            ]);

            if (! $data) {
                return response()->json(['Referral not found'], 404);
            }
            if (! isset($data->merchant->rewards) || ! count($data->merchant->rewards)) {
                return response()->json(['Can\'t find data about receiver reward'], 404);
            }

            return fractal($data)->transformWith(new ReceiverRewardTransformer)->toArray();
        } catch (\Exception $exception) {
            return response()->json([], 404);
        }
    }

    public function getReceiverCoupon(Request $request, $slug)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $data = $this->customers->withCriteria([
                new WithReceiverReward(),
            ])->findWhereFirst([
                'referral_slug' => $slug,
            ]);

            if (! $data) {
                return response()->json(['Referral not found'], 404);
            }
            if (! isset($data->merchant->rewards) || ! count($data->merchant->rewards)) {
                return response()->json(['Can\'t find data about receiver reward'], 404);
            }
        } catch (\Exception $exception) {
            return response()->json([], 404);
        }

        $receiver_email = $request->get('email');
        $merchant_id = $data->merchant->id;
        $merchant_reward = $data->merchant->rewards[0];

        // Get customer by email and merchant
        $this->customers->clearEntity();

        try {
            $customer = $this->customers->findWhereFirst([
                'merchant_id' => $merchant_id,
                'email'       => $receiver_email,
            ]);

            // Check if customer ID != referral customer ID
            if ($data->id === $customer->id) {
                return response()->json(['Unacceptable request: Reward discount cannot be self-given.'], 406);
            }
        } catch (\Exception $exception) {
            $this->customers->clearEntity();
            // Register customer by email and merchant

            $new_customer_data = [
                'email' => $receiver_email,
            ];

            $customer = app('customer_service')->updateOrCreate($data->merchant, $new_customer_data);
        }

        // Check if customer haven't already received reward
        try {
            $checkCoupon = $this->coupons->findWhereFirst([
                'merchant_id'        => $merchant_id,
                'customer_id'        => $customer->id,
                'merchant_reward_id' => $merchant_reward->id,
            ]);

            if ($checkCoupon) {
                return response()->json(['Unacceptable request: Reward discount cannot be given more than one time.'], 406);
            }
        } catch (\Exception $exception) {
            //
        }

        // Generate coupon
        try {
            $generatedCoupon = app('coupon_service')->generateRewardCoupon($merchant_reward->id, $customer->id, null, $data->id, [
                    'orders_count'                 => '0',
                    'prerequisite_customer_emails' => [$customer->email],
                ]);
        } catch (\Exception $exception) {
            //$generatedCoupon = Coupon::find(6);
            return response()->json([
                'Cannot generate discount at this moment. Please try again.',
                $exception->getMessage(),
            ], 500);
        }

        // Mark customer as referred by $data->id
        $this->referrals->create([
            'referral_customer_id' => $data->id,
            'invited_customer_id'  => $customer->id,
        ]);

        // Send email to customer
        event(new ReferralReceiverRewardCouponGenerated($customer, $merchant_reward, $generatedCoupon));

        // Return coupon
        return fractal($generatedCoupon)->transformWith(new CouponTransformer)->toArray();
    }

    public function sendReferralEmail(ReferralEmailRequest $request)
    {
        $merchantId = $request->get('merchant_id');
        $customerId = $request->get('customer_id');

        // Check if referral system enabled
        try {
            $merchantReferralSettings = $this->referralSettings->findWhereFirst([
                'merchant_id' => $merchantId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Referral program is not available for this merchant.'], 405);
        }

        if (! $merchantReferralSettings->program_status) {
            return response()->json(['message' => 'Referral program is not available for this merchant.'], 405);
        }

        // Check if receiver reward exists
        try {
            $receiverReward = $this->merchantRewards->withCriteria([
                new ByMerchant($merchantId),
            ])->findWhereFirst([
                'type_id'     => MerchantReward::REWARD_TYPE_REFERRAL_RECEIVER,
                'active_flag' => 1,
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Receiver reward not provided.'], 405);
        }

        // Check if customer not exists or did not receive reward earlier
        try {
            $customer = $this->customers->withCriteria([
                new ByMerchant($merchantId),
            ])->findWhereFirst(['email' => $request->get('email')]);

            if ($customer) {
                if ($customer->id === $customerId) {
                    return response()->json(['message' => 'Receiver can not be same as sender.'], 405);
                }
                try {
                    $coupon = $this->coupons->withCriteria([
                        new ByCustomer($customer->id),
                    ])->findWhereFirst([
                        'merchant_reward_id' => $receiverReward->id,
                    ]);

                    if ($coupon) {
                        return response()->json(['message' => 'Customer has already received referral reward.'], 405);
                    }
                } catch (\Exception $e) {
                    // Ok
                }
            }
        } catch (\Exception $e) {
            // Ok
        }

        event(new ReferralShareEmail($customerId, $receiverReward, $request->get('name'), $request->get('email'), $request->get('subject'), $request->get('body')));

        return response()->json([], 200);
    }

    protected function formatRewardIconOutput($url)
    {
        return trim($url) ? '<img src="'.$url.'" height="84" border="0" alt="" />' : '';
    }
}