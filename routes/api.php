<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
*/

Route::group([
    'middleware' => 'auth:api',
], function () {

    // Account Settings
    Route::get('/user/settings', 'Api\User\UserSettingsController@get');
    Route::put('/user/settings', 'Api\User\UserSettingsController@update');

    // Invitations...
    Route::get('/merchant/{merchant}/settings/invitations', 'Api\Settings\Merchants\InvitationController@all');
    Route::get('/merchant/{merchant}/settings/invited-users', 'Api\Settings\Merchants\InvitationController@getAllInvitedUsers');
    Route::post('/merchant/{merchant}/settings/invited-users/delete', 'Api\Settings\Merchants\InvitationController@removeInvitedUser');
    Route::post('/merchant/{merchant}/settings/invitations', 'Api\Settings\Merchants\InvitationController@store');
    Route::put('/merchant/{merchant}/settings/invitations', 'Api\Settings\Merchants\InvitationController@update');
    Route::delete('/invitations/{invitation}', 'Api\Settings\Merchants\InvitationController@destroy');

    // Merchant Store Integration
    Route::post('/merchants/{merchant}/store/products', 'Api\Merchant\MerchantStoreIntegrationController@getProducts')
        ->middleware('ownsMerchant');

    Route::get('/merchants/{merchant}/integrations/{integration}', 'Api\Merchant\MerchantIntegrationController@find')
        ->middleware('ownsMerchant');
    Route::put('/merchants/{merchant}/integrations/{integration}', 'Api\Merchant\MerchantIntegrationController@update')
        ->middleware('ownsMerchant');
    Route::post('/merchants/{merchant}/integrations/{integration}/reinstall-widget-code', 'Api\Merchant\MerchantIntegrationController@reinstallWidgetCode')
        ->middleware('ownsMerchant');

    // Customer widget impersonating
    Route::get('/merchants/{merchant}/customers/{customerId}/widget-impersonate', 'Api\Merchant\MerchantIntegrationController@impersonationConfig')
        ->middleware('ownsMerchant');

    // Plans
    Route::get('/plans', 'Api\Plan\PlanController@getPlansWithFeatures');

    // Merchant Rewards
    Route::get('/merchants/{merchant}/referrals/rewards/receiver/{rewardId?}', 'Api\Merchant\MerchantRewardController@getReceiverReward')
        ->middleware('ownsMerchant');
    Route::post('/merchants/{merchant}/referrals/rewards/receiver', 'Api\Merchant\MerchantRewardController@storeReceiverReward')
        ->middleware('ownsMerchant');
    Route::put('/merchants/{merchant}/rewards/{rewardId}', 'Api\Merchant\MerchantRewardController@update')
        ->middleware('ownsMerchant');
    Route::delete('/merchants/{merchant}/rewards/{rewardId}', 'Api\Merchant\MerchantRewardController@delete')
        ->middleware('ownsMerchant');

    Route::group([
        'prefix' => 'merchants',
    ], function () {
        /*
         * Route group
         *
         * /api/merchants
         *
         */

        Route::post('/', 'Api\Merchant\MerchantController@store');

        Route::group([
            'prefix'     => '{merchant}',
            'middleware' => ['ownsMerchant'],
        ], function () {
            /*
             * Route group
             *
             * /api/merchants/{merchant}
             *
             */

            // Integrations
            Route::get('/integrations', 'Api\Merchant\MerchantIntegrationController@get');
            Route::get('/integrations/ecommerce/active', 'Api\Merchant\MerchantIntegrationController@getActiveEcommerceIntegration');

            Route::group([
                'prefix' => 'referrals',
            ], function () {
                /*
                 * Route group
                 *
                 * /api/merchants/{merchant}/referrals
                 *
                 */

                // Referrals Sharing
                Route::get('/sharing', 'Api\Settings\Referrals\SharingController@get');
                Route::post('/sharing', 'Api\Settings\Referrals\SharingController@store');

                Route::get('/activity', 'Api\Settings\Referrals\ReferralsController@getActivity');
            });

            // Tags
            Route::get('/tags', 'Api\Merchant\MerchantTagController@get');

            // Tiers
            Route::get('/tiers', 'Api\Merchant\MerchantTierController@get');

            //
            Route::post('/tiers/{tier?}', 'Api\Merchant\MerchantTierController@store');
            Route::get('/tiers/{tierId}/restrictions', 'Api\Merchant\MerchantTierController@getTierRestrictions');

            // Action Restrictions
            Route::get('/actions/{actionId}/restrictions', 'Api\Merchant\MerchantActionController@getActionRestrictions');

            // Rewards Restrictions
            Route::get('/rewards/{rewardId}/restrictions', 'Api\Merchant\MerchantRewardController@getRewardRestrictions');

            //
            Route::post('/earning/actions/{action?}', 'Api\Merchant\Earning\MerchantActionController@store');

            // Plan
            Route::get('/plan', 'Api\Merchant\MerchantPlanController@getCurrentPlan');
            Route::post('/plan/upgrade', 'Api\Merchant\MerchantPlanController@upgradePlan');

            Route::get('/subscription', 'Api\Merchant\MerchantSubscriptionController@getCurrentSubscription');

            Route::put('/cancel-trial-subscription', 'Api\Merchant\MerchantSubscriptionController@cancelTrialSubscription');

            // Payment method
            Route::put('/payment-method', 'Api\Merchant\MerchantPaymentMethodController@update');

            // Details
            Route::get('/details', 'Api\Merchant\MerchantDetailsController@getDetails');
            Route::get('/details/all', 'Api\Merchant\MerchantDetailsController@getAllDetails');

            // Settings
            Route::get('/settings', 'Api\Merchant\MerchantController@getSettings');
            Route::get('/settings/common', 'Api\Merchant\MerchantController@getCommonSettings');
            Route::get('/reset-api-key', 'Api\Merchant\MerchantController@generateSecuredHashString');

            // Email Notifications
            Route::group(['prefix' => 'email-notifications'], function () {
                Route::post('/{group}/{type}/send-test-email', 'Api\EmailNotifications\EmailNotificationController@sendTestEmailNotification');
            });
        });
    });
});

//Route::get('/widget/referral/{}', function(){
//    return 'test';
//});

// Widget
Route::group([
    'prefix' => 'widget',
], function () {
    Route::group([
        'middleware' => [
            'widget.valid-connection',
        ],
    ], function () {
        Route::post('/customer/authCheck', 'Api\Widget\WidgetCustomerController@authCheck');
        Route::post('/settings', 'Api\Widget\WidgetController@widgetSettings');
        Route::post('/merchant-settings', 'Api\Widget\WidgetController@merchantSettings');
        Route::post('/point-settings', 'Api\Widget\WidgetController@pointSettings');
        Route::post('/vip-settings', 'Api\Widget\WidgetController@vipSettings');
        Route::post('/rewards', 'Api\Widget\WidgetRewardController@getRewards');
        Route::post('/rewards/{id}', 'Api\Widget\WidgetRewardController@getReward');
        Route::post('/actions', 'Api\Widget\WidgetActionController@getActions');
        Route::post('/actions/{slug}', 'Api\Widget\WidgetActionController@getAction');
        Route::post('/tier/{id}', 'Api\Widget\WidgetTierController@getTier');
        Route::post('/tiers', 'Api\Widget\WidgetTierController@getTiers');
        Route::post('/referral/{slug}/reward', 'Api\Widget\WidgetReferralController@getReceiverReward');
        Route::post('/referral/{slug}/coupon', 'Api\Widget\WidgetReferralController@getReceiverCoupon');
        Route::post('/coupons/{code}', 'Api\Widget\WidgetCouponController@getByCode');

        // Sharing settings
        Route::post('/referrals/sharing', 'Api\Widget\WidgetController@sharingSettings');
    });
    Route::group([
        'middleware' => [
            'widget.valid-connection',
            'widget.customer-auth',
        ],
    ], function () {
        Route::post('/customer', 'Api\Widget\WidgetCustomerController@getCustomer');
        Route::post('/customer/points-activity', 'Api\Widget\WidgetCustomerController@getCustomerPointsActivity');
        Route::post('/customer/referral-activity', 'Api\Widget\WidgetCustomerController@getCustomerReferralActivity');
        Route::post('/customer/rewards', 'Api\Widget\WidgetCustomerController@getCustomerRewards');
        Route::post('/customer/actions', 'Api\Widget\WidgetCustomerController@getCustomerActions');
        Route::post('/customer/rewards/{id}', 'Api\Widget\WidgetCustomerController@findCustomerReward');
        Route::put('/customer/birthday', 'Api\Widget\WidgetCustomerController@updateBirthday');
        Route::post('/customer/shares', 'Api\Widget\WidgetCustomerController@incrementShares');

        // Send referral email
        Route::post('/customer/referral-email', 'Api\Widget\WidgetReferralController@sendReferralEmail');

        // Redeem reward
        Route::post('/rewards/{rewardId}/redeem', 'Api\Widget\WidgetRewardController@redeemReward');

        // Complete action
        Route::post('/actions/{actionId}/complete', 'Api\Widget\WidgetActionController@completeAction');
    });

    //Get merchant plan
    Route::get('/merchants/{merchant}/plan', 'Api\Merchant\MerchantPlanController@getCurrentPlan' );
});

Route::get('/check_subscriptions', 'Api\Subscriptions\SubscriptionsController@checkSubscriptions');

Route::group(['prefix' => 'zapier'], function () {
    Route::get('/auth', 'Api\Integration\ZapierController@auth');
    Route::post('/subscribe', 'Api\Integration\ZapierController@subscribe');
    Route::delete('/unsubscribe', 'Api\Integration\ZapierController@unsubscribe');

    Route::post('/point_trigger', 'Api\Integration\ZapierController@point_trigger');
    Route::post('/deduct_point_trigger', 'Api\Integration\ZapierController@deduct_point_trigger');
    Route::post('/reward_trigger', 'Api\Integration\ZapierController@reward_trigger');

    Route::get('/sample', 'Api\Integration\ZapierController@sample');
});

Route::group(['prefix' => 'custom'], function () {
    Route::get('/customer/{customer_id}/point-balance', 'Api\Integration\CustomIntegrationController@getSinglePointBalance');
    Route::get('/customers/point-balance', 'Api\Integration\CustomIntegrationController@getMultiplePointBalance');
});
