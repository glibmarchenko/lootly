<?php

namespace App\Providers;

use App\Repositories\Contracts\ActionRepository;
use App\Repositories\Contracts\CouponRepository;
use App\Repositories\Contracts\CurrencyRepository;
use App\Repositories\Contracts\CustomerReferralClickRepository;
use App\Repositories\Contracts\CustomerReferralShareRepository;
use App\Repositories\Contracts\CustomerRepository;
use App\Repositories\Contracts\CustomerTransactionFlagRepository;
use App\Repositories\Contracts\IntegrationRepository;
use App\Repositories\Contracts\InvitationRepository;
use App\Repositories\Contracts\MerchantActionRestrictionRepository;
use App\Repositories\Contracts\MerchantRewardRestrictionRepository;
use App\Repositories\Contracts\MerchantDetailsRepository;
use App\Repositories\Contracts\MerchantRewardRepository;
use App\Repositories\Contracts\MerchantUserRepository;
use App\Repositories\Contracts\NotificationSettingsRepository;
use App\Repositories\Contracts\OrderRepository;
use App\Repositories\Contracts\PaymentRepository;
use App\Repositories\Contracts\PlanRepository;
use App\Repositories\Contracts\PointRepository;
use App\Repositories\Contracts\MerchantActionRepository;
use App\Repositories\Contracts\PointSettingsRepository;
use App\Repositories\Contracts\ReferralRepository;
use App\Repositories\Contracts\ReferralSettingsRepository;
use App\Repositories\Contracts\ReferralSharingRepository;
use App\Repositories\Contracts\RewardRepository;
use App\Repositories\Contracts\SubscriptionRepository;
use App\Repositories\Contracts\TagRepository;
use App\Repositories\Contracts\TierBenefitRepository;
use App\Repositories\Contracts\TierHistoryRepository;
use App\Repositories\Contracts\TierRepository;
use App\Repositories\Contracts\MerchantRepository;
use App\Repositories\Contracts\RewardCouponRepository;

use App\Repositories\Contracts\TierRestrictionRepository;
use App\Repositories\Contracts\TierSettingsRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Contracts\WidgetSettingsRepository;
use App\Repositories\Eloquent\EloquentActionRepository;
use App\Repositories\Eloquent\EloquentCouponRepository;
use App\Repositories\Eloquent\EloquentCurrencyRepository;
use App\Repositories\Eloquent\EloquentRewardCouponRepository;
use App\Repositories\Eloquent\EloquentCustomerReferralClickRepository;
use App\Repositories\Eloquent\EloquentCustomerReferralShareRepository;
use App\Repositories\Eloquent\EloquentCustomerRepository;
use App\Repositories\Eloquent\EloquentCustomerTransactionFlagRepository;
use App\Repositories\Eloquent\EloquentIntegrationRepository;
use App\Repositories\Eloquent\EloquentInvitationRepository;
use App\Repositories\Eloquent\EloquentMerchantActionRestrictionRepository;
use App\Repositories\Eloquent\EloquentMerchantRewardRestrictionRepository;
use App\Repositories\Eloquent\EloquentMerchantDetailsRepository;
use App\Repositories\Eloquent\EloquentMerchantRepository;
use App\Repositories\Eloquent\EloquentMerchantRewardRepository;
use App\Repositories\Eloquent\EloquentMerchantUserRepository;
use App\Repositories\Eloquent\EloquentNotificationSettingsRepository;
use App\Repositories\Eloquent\EloquentOrderRepository;
use App\Repositories\Eloquent\EloquentPaymentRepository;
use App\Repositories\Eloquent\EloquentPlanRepository;
use App\Repositories\Eloquent\EloquentPointRepository;
use App\Repositories\Eloquent\EloquentMerchantActionRepository;
use App\Repositories\Eloquent\EloquentPointSettingsRepository;
use App\Repositories\Eloquent\EloquentReferralRepository;
use App\Repositories\Eloquent\EloquentReferralSettingsRepository;
use App\Repositories\Eloquent\EloquentReferralSharingRepository;
use App\Repositories\Eloquent\EloquentRewardRepository;
use App\Repositories\Eloquent\EloquentSubscriptionRepository;
use App\Repositories\Eloquent\EloquentTagRepository;
use App\Repositories\Eloquent\EloquentTierBenefitRepository;
use App\Repositories\Eloquent\EloquentTierHistoryRepository;
use App\Repositories\Eloquent\EloquentTierRepository;
use App\Repositories\Eloquent\EloquentTierRestrictionRepository;
use App\Repositories\Eloquent\EloquentTierSettingsRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentWidgetSettingsRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PointRepository::class, EloquentPointRepository::class);
        $this->app->bind(MerchantActionRepository::class, EloquentMerchantActionRepository::class);
        $this->app->bind(MerchantRewardRepository::class, EloquentMerchantRewardRepository::class);
        $this->app->bind(TierRepository::class, EloquentTierRepository::class);
        $this->app->bind(WidgetSettingsRepository::class, EloquentWidgetSettingsRepository::class);
        $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
        $this->app->bind(CouponRepository::class, EloquentCouponRepository::class);
        $this->app->bind(MerchantRepository::class, EloquentMerchantRepository::class);
        $this->app->bind(ReferralRepository::class, EloquentReferralRepository::class);
        $this->app->bind(RewardRepository::class, EloquentRewardRepository::class);
        $this->app->bind(IntegrationRepository::class, EloquentIntegrationRepository::class);
        $this->app->bind(OrderRepository::class, EloquentOrderRepository::class);
        $this->app->bind(CustomerTransactionFlagRepository::class, EloquentCustomerTransactionFlagRepository::class);
        $this->app->bind(TierHistoryRepository::class, EloquentTierHistoryRepository::class);
        $this->app->bind(ReferralSharingRepository::class, EloquentReferralSharingRepository::class);
        $this->app->bind(MerchantDetailsRepository::class, EloquentMerchantDetailsRepository::class);
        $this->app->bind(TierSettingsRepository::class, EloquentTierSettingsRepository::class);
        $this->app->bind(TierBenefitRepository::class, EloquentTierBenefitRepository::class);
        $this->app->bind(PointSettingsRepository::class, EloquentPointSettingsRepository::class);
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(InvitationRepository::class, EloquentInvitationRepository::class);
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
        $this->app->bind(ActionRepository::class, EloquentActionRepository::class);
        $this->app->bind(MerchantActionRestrictionRepository::class, EloquentMerchantActionRestrictionRepository::class);
        $this->app->bind(MerchantRewardRestrictionRepository::class, EloquentMerchantRewardRestrictionRepository::class);
        $this->app->bind(TierRestrictionRepository::class, EloquentTierRestrictionRepository::class);
        $this->app->bind(PlanRepository::class, EloquentPlanRepository::class);
        $this->app->bind(PaymentRepository::class, EloquentPaymentRepository::class);
        $this->app->bind(SubscriptionRepository::class, EloquentSubscriptionRepository::class);
        $this->app->bind(MerchantUserRepository::class, EloquentMerchantUserRepository::class);
        $this->app->bind(CustomerReferralShareRepository::class, EloquentCustomerReferralShareRepository::class);
        $this->app->bind(CustomerReferralClickRepository::class, EloquentCustomerReferralClickRepository::class);
        $this->app->bind(CurrencyRepository::class, EloquentCurrencyRepository::class);
        $this->app->bind(RewardCouponRepository::class, EloquentRewardCouponRepository::class);
        $this->app->bind(NotificationSettingsRepository::class, EloquentNotificationSettingsRepository::class);
        $this->app->bind(ReferralSettingsRepository::class, EloquentReferralSettingsRepository::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
