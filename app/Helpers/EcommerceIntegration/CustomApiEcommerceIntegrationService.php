<?php

namespace App\Helpers\EcommerceIntegration;

use App\Helpers\EcommerceIntegration\Exceptions\ApiClientSetupError;
use App\Mail\MerchantCouponsEmpty;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\RewardCouponRepository;
use App\Repositories\UserRepository;
use App\Merchant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomApiEcommerceIntegrationService extends BaseEcommerceIntegrationService
{
    protected $merchantDetails;
    protected $rewardCoupon;
    protected $users;

    /**
     * CustomApiEcommerceIntegrationService constructor.
     *
     * @param \App\Repositories\Contracts\PointRepository $points
     * @param \App\Repositories\Contracts\MerchantDetailsRepository $merchantDetails
     * @param \App\Repositories\UserRepository $users
     * @param \App\Repositories\RewardCouponRepository $rewardCoupon
     */
    public function __construct(
        PointRepository $points,
        MerchantDetailsRepository $merchantDetails,
        UserRepository $users,
        RewardCouponRepository $rewardCoupon
    ) {
        parent::__construct($points);

        $this->merchantDetails = $merchantDetails;
        $this->rewardCoupon = $rewardCoupon;
        $this->users = $users;
    }

    public function apiClient()
    {
        return null;
    }

    protected function sendUninstallIntegrationRequest()
    {
        //
    }

    protected function sendGenerateDiscountRequest($discountData)
    {
        $merchant = Merchant::find($this->merchantReward->merchant_id);
        $countRewardCoupon = $this->rewardCoupon->countAvailableByMerchantRewardId($this->merchantReward->id);

        if ($countRewardCoupon <= 1) {
            $user = $this->users->find($merchant->owner_id);

            if ($user) {
                $notification = $user->notification_types()
                    ->where('slug', 'no_reward_codes_available')
                    ->where('status', 1)
                    ->first();

                if ($notification && $notification->pivot && $notification->pivot->active) {
                    try {
                        Mail::to($user->email)->queue(new MerchantCouponsEmpty($merchant, $this->merchantReward));

                    } catch (\Exception $e) {
                        Log::error('An error occurred while attempting to send email notification to user #' . $user->id . ' on merchant #' . $merchant->id . ' coupons are empty.' . $e->getMessage());
                    }
                }
            }
        }

        $rewardCoupon = $this->rewardCoupon->findAvailableByMerchantRewardId($this->merchantReward->id);

        if (!$rewardCoupon) {
            throw new \Exception('There are no coupons available to send, Please add more.');
        }

        $this->rewardCoupon->setRedeemed($rewardCoupon->id);

        return [
            'id' => $rewardCoupon->id,
            'code' => $rewardCoupon->code,
        ];
    }

    protected function applyDiscountRestrictions(array $discountData, array $restrictions)
    {
        return $discountData;
    }

    protected function sendGetCustomerRequest($customerId)
    {
        $output = [];

        return $output;
    }

    protected function sendGetProductsRequest($requestParams)
    {
        $output = [];

        return $output;
    }

    protected function getFreeShippingDiscountData($discountReward, $discountSettings = [], $restrictions = [])
    {
        $discountData = [];

        return $discountData;
    }

    protected function getFreeProductDiscountData($discountReward, $discountSettings = [], $couponValue = 0, $product_ids = [], $restrictions = [])
    {
        $discountData = [];

        return $discountData;
    }
}
