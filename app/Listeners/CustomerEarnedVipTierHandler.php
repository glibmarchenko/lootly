<?php

namespace App\Listeners;

use App\Events\CustomerEarnedPointsForAction;
use App\Events\CustomerEarnedVipTier;
use App\Models\MerchantReward;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Contracts\TierBenefitRepository;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\ByMerchant;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class CustomerEarnedVipTierHandler
{
    protected $customers;

    protected $merchants;

    protected $tiers;

    protected $tierBenefits;

    protected $pointSettings;

    /**
     * Create the event listener.
     *
     * @param \App\Repositories\Contracts\CustomerRepository      $customers
     * @param \App\Repositories\Contracts\MerchantRepository      $merchants
     * @param \App\Repositories\Contracts\TierRepository          $tiers
     * @param \App\Repositories\Contracts\TierBenefitRepository   $tierBenefits
     * @param \App\Repositories\Contracts\PointSettingsRepository $pointSettings
     */
    public function __construct(
        CustomerRepository $customers,
        MerchantRepository $merchants,
        TierRepository $tiers,
        TierBenefitRepository $tierBenefits,
        PointSettingsRepository $pointSettings
    ) {
        $this->customers = $customers;
        $this->merchants = $merchants;
        $this->tiers = $tiers;
        $this->tierBenefits = $tierBenefits;
        $this->pointSettings = $pointSettings;
    }

    /**
     * @param \App\Events\CustomerEarnedVipTier $event
     */
    public function handle(CustomerEarnedVipTier $event)
    {
        $customer = $event->customer;
        $tier = $event->tier;
        $coupons = $event->coupons;

        if (env('APP_PRODUCTION_MODE', true)) {
            return;
        }

        if ($customer && $tier) {
            $merchant = $this->merchants->withCriteria([
                new EagerLoad(['points_settings']),
            ])->find($customer->merchant_id);

            $pointSettings = $merchant->points_settings;
            if (! $pointSettings) {
                $pointSettings = $this->pointSettings->getDefaults();
            }

            $tierBenefits = $this->tierBenefits->findWhere([
                'tier_id' => $tier->id,
            ]);

            $benefitList = [];

            if (trim($tier->multiplier_text)) {
                $benefitList[] = trim($tier->multiplier_text);
            }

            $availableRewardsList = [];

            if (count($tierBenefits)) {
                // Get available rewards list
                for ($i = 0; $i < count($tierBenefits); $i++) {
                    if (trim($tierBenefits[$i]->merchant_reward_id)) {
                        for ($j = 0; $j < count($coupons); $j++) {
                            if ($tierBenefits[$i]->merchant_reward_id == $coupons[$j]->merchant_reward_id) {
                                $availableRewardsList[] = [
                                    'title'  => $tierBenefits[$i]->benefits_discount,
                                    'coupon' => $coupons[$j]->coupon_code,
                                ];

                                unset($coupons[$j]);
                                $coupons = array_values($coupons);

                                break;
                            }
                        }
                    } else {
                        if ($tierBenefits[$i]->benefits_reward == 'points') {
                            $benefitList[] = $tierBenefits[$i]->benefits_discount.' Free Points';
                        } else {
                            if ($tierBenefits[$i]->benefits_type == 'custom') {
                                $benefitList[] = $tierBenefits[$i]->benefits_discount;
                            }
                        }
                    }
                }
            }

            //Log::info('Available Rewards: '.print_r($availableRewardsList, true));
            //Log::info('Befit List: '.print_r($benefitList, true));

            $availableRewardsListHtml = '';
            if (count($availableRewardsList)) {
                foreach ($availableRewardsList as $availableReward) {
                    $availableRewardsListHtml .= '
                        <tr>
                            <td class="img" style="padding-bottom: 14px; font-size:0pt; line-height:0pt; text-align:left;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="text" width="15" valign="top" style="color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:20px; text-align:left;"><strong>&bull;</strong></td>
                                        <td class="text" valign="top" style="color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:20px; text-align:left;">
                                            '.$availableReward['title'].': <strong>'.$availableReward['coupon'].'</strong>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        ';
                }
            }

            $benefitListHtml = '';
            if (count($benefitList)) {
                foreach ($benefitList as $benefit) {
                    $benefitListHtml .= '
                        <tr>
                            <td class="img pb-14" style="padding-bottom: 22px; font-size:0pt; line-height:0pt; text-align:left;">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="text" width="15" valign="top" style="color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:20px; text-align:left;"><strong>&bull;</strong></td>
                                        <td class="text" valign="top" style="color:#2a2a2a; font-family:Arial,sans-serif; font-size:16px; line-height:20px; text-align:left;">
                                            '.$benefit.'
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>   
                    ';
                }
            }

            $tags = [
                '{vip-tier-entry-text}'    => $tier->text_email,
                // !!! IMPORTANT TO BE ON TOP because it may include other tags !!!
                '{customer}'               => trim($customer->name) ?: 'Dear customer',
                '{points-name}'            => $pointSettings->plural_name,
                '{tier-name}'              => $tier->name,
                '{vip-tier-icon}'          => $this->formatRewardIconOutput(app('email_notification_settings_service')->getTierIconUrl($tier, 'vip-tier-icon', 'points_vip_tier_earned')),
                '{available-rewards-list}' => $availableRewardsListHtml,
                '{benefit-list}'           => $benefitListHtml,
                '{button-link}'            => $merchant->website,
            ];

            // Send email notification to customer
            app('email_notification')->send('points_vip_tier_earned', $merchant, $customer->name, $customer->email, $tags);

            // Trigger Zapier webhook
            if ($merchant->integrations->filter(function ($integration) {return $integration->slug == 'zapier';})->count()) {
                app('App\Services\Zapier\ZapierHook')->send($merchant, 'vip-tier-earned', [
                    'email' => $customer->email,
                    'tier'  => $tier->name
                ]);
            }
        }
    }

    protected function formatRewardIconOutput($url)
    {
        return trim($url) ? '<img src="'.$url.'" height="84" border="0" alt="" />' : '';
    }
}
