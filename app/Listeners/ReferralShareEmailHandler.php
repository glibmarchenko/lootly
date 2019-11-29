<?php

namespace App\Listeners;

use App\Events\CustomerSpentPointsForReward;
use App\Events\ReferralShareEmail;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Eloquent\Criteria\ByCustomer;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ReferralShareEmailHandler
{
    protected $customers;

    protected $merchants;

    protected $pointSettings;

    protected $points;

    protected $merchantRewards;

    /**
     * Create the event listener.
     *
     * @param \App\Repositories\Contracts\CustomerRepository       $customers
     * @param \App\Repositories\Contracts\MerchantRepository       $merchants
     * @param \App\Repositories\Contracts\PointSettingsRepository  $pointSettings
     *
     * @param \App\Repositories\Contracts\PointRepository          $points
     *
     * @param \App\Repositories\Contracts\MerchantRewardRepository $merchantRewards
     *
     */
    public function __construct(
        CustomerRepository $customers,
        MerchantRepository $merchants,
        PointSettingsRepository $pointSettings,
        PointRepository $points,
        MerchantRewardRepository $merchantRewards
    ) {
        $this->customers = $customers;
        $this->merchants = $merchants;
        $this->pointSettings = $pointSettings;
        $this->points = $points;
        $this->merchantRewards = $merchantRewards;
    }

    /**
     * @param \App\Events\ReferralShareEmail $event
     */
    public function handle(ReferralShareEmail $event)
    {
        $sender = $this->customers->find($event->customerId);

        $merchant = $this->merchants->withCriteria([
            new EagerLoad(['referrals_settings'])
        ])->find($sender->merchant_id);

        if ( isset( $merchant->referrals_settings ) && trim( $merchant->referrals_settings->referral_link ) && !empty( $merchant->referrals_settings->referral_link ) ) {
        $referralLink = rtrim(trim($merchant->referrals_settings->referral_link),'/').'/?loref='.$sender->referral_slug;
            if ( false === strpos( $referralLink, '://' ) ) {
                $referralLink = 'http://' . $referralLink;
            }
        }
        elseif (isset($merchant->referrals_settings) && $merchant->referrals_settings->referral_domain_status && trim($merchant->referrals_settings->referral_domain)){
            $referralLink = rtrim(trim($merchant->referrals_settings->referral_domain), '/').'/?loref='.$sender->referral_slug;
        }
        else{
            $referralLink = rtrim(env('REFERRAL_LINKS_DOMAIN', 'http://ref.lootly.io'), '/').'/'.$sender->referral_slug;
        }

        $emailSubject = htmlspecialchars($event->emailSubject);

        $emailBody = htmlspecialchars($event->emailBody);

        $emailBody = nl2br($emailBody);

        $tags = [
            '{email-body}'    => $emailBody,
            // !!! IMPORTANT TO BE ON TOP because it may include other tags !!!
            '{receiver-name}' => $event->receiverName,
            '{sender-name}'   => $sender->name,
            '{button-link}'   => $referralLink,
            '{reward-name}'   => $event->receiverReward->reward_text,
            '{reward-icon}'   => $this->formatRewardIconOutput(app('email_notification_settings_service')->getRewardIconUrl($event->receiverReward, 'reward-icon', 'referral_share_email')),
        ];

        // Send email notification to customer
        app('email_notification')->send('referral_share_email', $merchant, $event->receiverName, $event->receiverEmail, $tags, $emailSubject);
    }

    protected function formatRewardIconOutput($url)
    {
        return trim($url) ? '<img src="'.$url.'" height="84" border="0" alt="" />' : '';
    }
}
