<?php

/*
|--------------------------------------------------------------------------
| Website Pages Routes
|--------------------------------------------------------------------------
|
| Here is where we register all of the routes for the website.
| i.e. Homepage, Pricing, About, Company .. etc
|
*/

Route::get('/', function () {
    return view('website.home');
});
Route::group(['prefix' => '/compare'], function () {
    Route::get('/smile', function () {
        return view('website.compare.smile');
    });
    Route::get('/swell-rewards', function () {
        return view('website.compare.swell');
    });
    Route::get('/loyalty-lion', function () {
        return view('website.compare.loyalty-lion');
    });
});
Route::group(['prefix' => '/features'], function () {
    Route::get('/points-rewards', function () {
        return view('website.features.points-rewards');
    });
    Route::get('/vip', function () {
        return view('website.features.vip');
    });
    Route::get('/referrals', function () {
        return view('website.features.referrals');
    });
    Route::get('/insights', function () {
        return view('website.features.insights');
    });
});
Route::group(['prefix' => '/apps'], function () {
    Route::get('/', function () {
        return view('website.integrations.index');
    });

    Route::get('/shopify', function () {
        return view('website.integrations.shopify');
    });
    
    Route::get('/bigcommerce', function () {
        return view('website.integrations.bigcommerce');
    });
    
    Route::get('/magento', function () {
        return view('website.integrations.magento');
    });

    Route::get('/woocommerce', function () {
        return view('website.integrations.woocommerce');
    });

    Route::get('/volusion', function () {
        return view('website.integrations.volusion');
    });

    Route::get('/trustspot', function () {
        return view('website.integrations.trustspot');
    });
});
Route::group(['prefix' => '/resources'], function () {

    Route::get('/', 'ResourceController@index')->name('website.resources.index');

    Route::get('/vip-program', function () {
        return view('website.resources.articles.vip-program');
    });
    Route::get('/case-studies', function () {
        return view('website.resources.articles.case-studies');
    });
    Route::get('/audi-mods-case-study', function () {
        return view('website.resources.articles.audi-mods-case-study');
    });
    Route::get('/why-repeat-customers-are-cheaper', function () {
        return view('website.resources.articles.why-repeat-customers-cheaper');
    });

    Route::get('/{id}/{slug}', 'ResourceController@show')->name('website.resources.show');
});
Route::group(['prefix' => '/'], function () {

    Route::get('/request-demo', 'WelcomeController@requestDemo');

    Route::get('/about', function () {
        return view('website.company.about');
    });
    Route::get('/careers', function () {
        return view('website.company.careers');
    });
    Route::get('/our-customers', function () {
        return view('website.company.customers');
    });
    Route::get('/contact', function () {
        return view('website.company.contact');
    });
    Route::get('/press', function () {
        return view('website.company.press');
    });
    Route::get('/terms-of-service', function () {
        return view('website.company.terms-of-service');
    });
    Route::get('/privacy', function () {
        return view('website.company.privacy-policy');
    });
    Route::get('/faq', function () {
        return view('website.company.faq');
    });
    Route::get('/status', function () {
        return view('website.company.status');
    });
    Route::get('/pricing', function () {
        return view('website.company.pricing');
    });

    Route::post('/demo-submit', 'RequestDemoController@index')->name('demo.submit');
});

/*
|--------------------------------------------------------------------------
| APP user Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where we register all of the routes for the main App.
| i.e. Dashboard, Account settings, Features, Interations .. etc
|
*/

Route::get('/widget/{vue_capture?}', function () {
    return view('_widgets.widget');
})->where('vue_capture', '[\/\w\.-]*')->name('widgets.widget');

Route::get('/rewards-page/{api_key?}', 'Settings\Display\RewardPage\RewardSettingsController@get_widgets_view')
    ->name('rewards_page');

Route::get('/embedded/facebook-like/', function () {
    return view('_widgets._facebook-page');
});


/*
| --- Account and Dashboard Routes ---
*/
Route::group([
    'prefix'     => '/',
    'middleware' => ['auth'],
], function () {

    Route::get('/home', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::post('/dashboard/get-data', 'DashboardController@getStatistic')->name('dashboard.get-data');
});

Route::group([
    'prefix'     => 'account',
    'middleware' => ['auth'],
], function () {
    // Route::get('/settings', 'AccountController@settings')->name('account.settings');
    // Route::post('/settings', 'AccountController@settingsStore')->name('account.settings.store');

    Route::get('/upgrade', 'AccountController@upgrade')->name('account.upgrade');

    Route::get('/billing', 'AccountController@billing')->name('account.billing');

    Route::get('/get_pdf/{subsc_id}', 'AccountController@getBillPdf')->name('account.get_pdf');
});

/*
| --- Points Routes ---
*/
Route::group([
    'prefix'     => 'points',
    'middleware' => ['auth'],
], function () {

    Route::get('/', 'PointsController@index');
    // Route::get('/overview', 'PointsController@overview')->name('points.overview');
    Route::get('/activity', 'PointsController@activity')->name('points.activity');
    Route::get('/settings', 'PointsController@settings')->name('points.settings');

    Route::group(['prefix' => 'spending'], function () {

        // Route::get('/', 'PointsController@spending')->name('points.spending');
        // Route::get('/rewards', 'PointsController@spendingRewards')->name('points.spending.rewards');

        Route::group(['prefix' => 'actions'], function () {
            Route::get('/fixed-discount/{id?}', 'PointsController@fixedDiscount')
                ->name('points.spending.actions.fixed-discount.get');
            Route::get('/variable-discount/{id?}', 'PointsController@variableDiscount')
                ->name('points.spending.actions.variable-discount.get')
                ->middleware(['spending_permissions']);
            Route::get('/percentage-discount/{id?}', 'PointsController@percentageDiscount')
                ->name('points.spending.actions.percentage-discount.get');
            Route::get('/free-shipping-discount/{id?}', 'PointsController@freeShipping')
                ->name('points.spending.actions.free-shipping.get');
            Route::get('/free-product-discount/{id?}', 'PointsController@freeProduct')
                ->name('points.spending.actions.free-product.get');
            Route::get('/points/{id?}', 'PointsController@points')->name('points.spending.actions.points.get');
        });
    });

    Route::group(['prefix' => 'earning'], function () {

        // Route::get('/', 'PointsController@earning')->name('points.earning');
        // Route::get('/actions', 'PointsController@earningActions')->name('points.earning.actions');

        Route::group(['prefix' => 'actions'], function () {
            Route::get('/', 'PointsController@earningActions')->name('points.earning.actions');
            Route::get('/make-a-purchase', 'PointsController@makePurchase')
                ->name('points.earning.actions.make-a-purchase');

            Route::get('/create-account', 'PointsController@createAccount')
                ->name('points.earning.actions.create-account');
            Route::get('/celebrate-birthday', 'PointsController@celebrateBirthday')
                ->name('points.earning.actions.celebrate-birthday');
            Route::get('/facebook-like', 'PointsController@facebookLike')->name('points.earning.actions.facebook-like');
            Route::get('/facebook-share', 'PointsController@facebookShare')
                ->name('points.earning.actions.facebook-share');
            Route::get('/twitter-follow', 'PointsController@twitterFollow')
                ->name('points.earning.actions.twitter-follow');
            Route::get('/twitter-share', 'PointsController@twitterShare')->name('points.earning.actions.twitter-share');
            Route::get('/instagram-follow', 'PointsController@instagramFollow')
                ->name('points.earning.actions.instagram-follow');
            Route::get('/custom-earning', 'PointsController@customEarning')->name('points.earning.actions.custom-earning');

            Route::group(['middleware' => ['points_permissions']], function () {
                Route::get('/read-content', 'PointsController@readContent')
                    ->name('points.earning.actions.read-content');
                Route::get('/trustspot-review', 'PointsController@trustspotReview')
                    ->name('points.earning.actions.trustspot-review');
            });
            Route::get('/goal-spend', 'PointsController@goalSpend')->name('points.earning.actions.goal-spend');
            Route::get('/goal-orders', 'PointsController@goalOrders')->name('points.earning.actions.goal-orders');
        });
    });
});

/*  
| --- Customers Routes ---
*/

Route::group([
    'prefix'     => 'customers',
    'middleware' => ['auth'],
], function () {
    Route::get('/', 'CustomersController@index')->name('customers.index');

    Route::get('/profile/{id}', 'CustomersController@show')->name('customers.show');

    Route::get('/widget/{id}', function ($id) {
        return view('customers.show_in_widget')->withId($id);
    })->name('customers.show_in_widget');
});

/*
| --- VIP Routes --- 
*/
Route::group([
    'prefix'     => 'vip',
    'middleware' => ['auth'],
], function () {

    Route::get('/upgrade', function () {
        return view('vip.upgrade');
    })->name('vip.upgrade');

    Route::group(['prefix' => 'tiers'], function () {
        // Route::get('/', function() {
        // 	return view('vip.tiers.index');
        // })->name('vip.tiers');

        // Route::get('/add', function () {
        //     return view('vip.tiers.add-tier');
        // })->name('vip.tiers.add');

        // Route::get('/edit/{id}', function() {
        // 	return view('vip.tiers.edit-tier');
        // })->name('vip.tiers.edit');

    });
    // Route::get('/activity', function() {
    // 	return view('vip.activity');
    // })->name('vip.activity');

    // Route::get('/members', function() {
    // 	return view('vip.members');
    // })->name('vip.members');

    // Route::get('/settings', function() {
    // 	return view('vip.settings');
    // })->name('vip.settings');

});

/* 
| --- Referrals Routes ---
*/
Route::get('/referrals/upgrade', function () {
    return view('referrals.upgrade');
})->name('referrals.upgrade')->middleware('auth');

Route::group([
    'prefix'     => 'referrals',
    'middleware' => [
        'auth',
        'referral_permissions',
    ],
], function () {
    Route::get('/overview', 'ReferralController@overview')->name('referrals.overview');

    Route::group(['prefix' => 'rewards'], function () {
        Route::get('/', 'ReferralController@rewards')->name('referrals.reward');

        Route::group(['prefix' => 'sender'], function () {

            Route::get('/fixed-amount-discount', function () {
                return view('referrals.rewards.sender.fixed-discount');
            })->name('referrals.rewards.sender.fixed-discount.get');

            Route::get('/percentage-discount', function () {
                return view('referrals.rewards.sender.percentage-discount');
            })->name('referrals.rewards.sender.percentage-discount.get');

            Route::get('/free-shipping-discount', function () {
                return view('referrals.rewards.sender.free-shipping');
            })->name('referrals.rewards.sender.free-shipping.get');

            Route::get('/free-product-discount', function () {
                return view('referrals.rewards.sender.free-product');
            })->name('referrals.rewards.sender.free-product.get');

            Route::get('/points', function () {
                return view('referrals.rewards.sender.points');
            })->name('referrals.rewards.sender.points.get');
        });

        Route::group(['prefix' => 'receiver'], function () {

            Route::get('/fixed-amount-discount', function () {
                return view('referrals.rewards.receiver.fixed-discount');
            })->name('referrals.rewards.receiver.fixed-discount.get');

            Route::get('/percentage-discount', function () {
                return view('referrals.rewards.receiver.percentage-discount');
            })->name('referrals.rewards.receiver.percentage-discount.get');

            Route::get('/free-shipping-discount', function () {
                return view('referrals.rewards.receiver.free-shipping');
            })->name('referrals.rewards.receiver.free-shipping.get');

            Route::get('/free-product-discount', function () {
                return view('referrals.rewards.receiver.free-product');
            })->name('referrals.rewards.receiver.free-product.get');
        });
    });

    Route::get('/sharing', 'ReferralController@sharing')->name('referrals.sharing');

    Route::get('/activity', 'ReferralController@activity')->name('referrals.activity');

    Route::get('/activity/export', 'Settings\referrals\SettingsReferralsController@export')->name('referrals.activity.export');

    Route::post('/activity/get-data', 'ReferralController@getActivity')->name('referrals.activity.get-data');

    Route::get('/settings', 'Settings\referrals\SettingsReferralsController@view')->name('referrals.settings');
});

/*
| --- Integrations Routes ---
*/
Route::group([
    'prefix'     => 'integrations',
    'middleware' => ['auth'],
], function () {
    Route::get('/', 'IntegrationsController@index')->name('integrations');

    Route::get('/upgrade', function () {
        return view('integrations.upgrade');
    })->name('integrations.upgrade');

    Route::get('/overview', 'IntegrationsController@overview')->name('integrations.overview');

    Route::get('/manage', function () {
        return view('integrations.manage.index');
    })->name('integrations.manage');

    Route::get('/manage/edit/{slug}', 'IntegrationsController@edit')->name('integrations.manage.edit');
    Route::post('/store-suggestion', 'IntegrationsController@storeSuggestion')->name('integrations.store_suggestion');
});

/*  
| --- Onboarding Routes ---
*/
Route::group([
    'prefix'     => 'onboarding',
    'middleware' => ['auth'],
], function () {
    Route::group(['prefix' => 'shopify'], function () {

        Route::get('/', function () {
            return view('onboarding.shopify.index');
        })->name('onboarding.shopify.index');

        Route::get('/setup', function () {
            return view('onboarding.shopify.setup');
        })->name('onboarding.shopify.setup');
    });
});

/*
| --- Reports Routes ---
*/

Route::get('/reports/upgrade', function () {
    return view('reports.upgrade');
})->name('reports.upgrade')->middleware('auth');

Route::group([
    'prefix'     => 'reports',
    'middleware' => [
        'auth',
        'reports_permissions',
    ],
], function () {

    Route::get('/overview', 'ReportsController@overview')->name('reports.overview');

    Route::get('/referrals', 'ReportsController@referrals')->name('reports.referrals');

    Route::post('/overview/get-data', 'ReportsController@getOverviewData')
        ->name('reports.overview.get-data');

    Route::get('/referrals', 'ReportsController@referrals')
        ->name('reports.referrals');
    
    Route::post('/referrals/get-data', 'ReportsController@getReferralsData')
        ->name('reports.referrals.get-data');
});

/*
| --- Display Routes --- 
*/
Route::group([
    'prefix'     => 'display',
    'middleware' => ['auth'],
], function () {
    Route::group(['prefix' => 'widget'], function () {

        Route::get('/', function () {
            return view('display.widget.index');
        })->name('display.widget');

        Route::get('/branding', 'Settings\Display\Widget\BrandingSettingsController@view')
            ->name('display.widget.branding');

        Route::get('/tab', 'Settings\Display\Widget\WidgetSettingsController@tabView')->name('display.widget.tab');

        Route::get('/edit', 'Settings\Display\Widget\WidgetSettingsController@editView')
            ->name('display.widget.edit-not-logged-in');

        Route::get('/edit/logged-in', 'Settings\Display\Widget\WidgetLoggedSettingsController@editView')
            ->name('display.widget.edit-logged-in');
    });

    Route::group(['prefix' => 'email-notifications'], function () {
        Route::get('/', function () {
            return view('display.email.index');
        })->name('display.email');

        Route::get('/settings', 'Settings\Display\EmailNotification\EmailNotificationSettingsController@view')
            ->name('display.email.settings');

        Route::group(['prefix' => 'points'], function () {

            Route::get('/earned', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@pointsEarned')
                ->name('display.email.points.earned');

            Route::get('/spent', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@pointsSpent')
                ->name('display.email.points.spent');

            Route::get('/reward-available', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@rewardAviable')
                ->name('display.email.points.reward-available');

            Route::get('/point-expiration', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@pointExiration')
                ->name('display.email.points.point-expiration');

            Route::get('/vip-tier-earned', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@vipTierEarned')
                ->name('display.email.points.vip-tier-earned');
        });

        Route::group(['prefix' => 'referral'], function () {

            Route::get('/share-email', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@shareEmail')
                ->name('display.email.referral.share-email');

            Route::get('/receiver-reward', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@receiverReward')
                ->name('display.email.referral.receiver-reward');

            Route::get('/sender-reward', 'Settings\Display\EmailNotification\DisplayEmailNotificationController@senderReward')
                ->name('display.email.referral.sender-reward');
        });
    });

    Route::group([
        'prefix'     => 'reward-page',
        'middleware' => ['rewards_permissions'],
    ], function () {

        Route::get('/', function () {
            return view('display.reward-page.index');
        })->name('display.reward-page');

        Route::get('/settings', 'Settings\Display\RewardPage\RewardSettingsController@get_settings')
            ->name('display.reward-page.settings');

        Route::post('/settings/store', 'Settings\Display\RewardPage\RewardSettingsController@store')
            ->name('display.reward-page.settings.store');

        Route::get('/branding', 'Settings\Display\RewardPage\PageBrangingController@get')
            ->name('display.reward-page.branding');

        Route::post('/branding/store', 'Settings\Display\RewardPage\PageBrangingController@store')
            ->name('display.reward-page.branding.store');
    });
});

Route::get('/rewards/upgrade', 'Settings\Display\RewardPage\RewardSettingsController@upgrade')->name('rewards.upgrade');

Route::group(['middleware' => ['web']], function () {

    // Authentication...

    // Route::get('/login', 'Auth\SparkLoginController@showLoginForm')->name('login');
    Route::get('/login', function () {
        return view('website.auth.login');
    })->name('login');
    Route::post('/login', 'Auth\SparkLoginController@login');
    Route::get('/logout', 'Auth\SparkLoginController@logout')->name('logout');

    // Two-Factor Authentication Routes...
    Route::get('/login/token', 'Auth\SparkLoginController@showTokenForm');
    Route::post('/login/token', 'Auth\SparkLoginController@verifyToken');

    // Registration...
    Route::post('/signup', 'Auth\RegisterController@register')->name('website.signup');
    Route::get('/signup/{plan?}/{yearly?}', 'Auth\RegisterController@signup')->name('signup');
    Route::get('/register', 'Auth\SparkRegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'Auth\SparkRegisterController@register');

    // Forgot Password...
    Route::get('/password/reset/{token?}', 'Auth\SparkPasswordController@showResetForm')->name('password.reset');

    Route::get('/register/invait/{token}', 'Auth\SparkRegisterController@showRegistrationForm');

    Route::get('/select-account', 'SwitchMerchantController@index');

    // Integration apps endpoints
    Route::group(['prefix' => 'app'], function () {

        // Shopify
        Route::group(['prefix' => 'shopify'], function () {

            // Install/uninstall
            Route::get('install', 'ShopifyIntegrationController@install');
            Route::get('callback', 'ShopifyIntegrationController@callback');
            Route::get('finish', 'ShopifyIntegrationController@selectMerchantAndConnectShopify');

            // POS
            Route::get('promotions', 'ShopifyIntegrationController@promotions');
            Route::get('perform_action', 'ShopifyIntegrationController@performAction');
            Route::get('revert_action', 'ShopifyIntegrationController@revertAction');
        });

        // Trustspot
        Route::group(['prefix' => 'trustspot'], function () {
            Route::get('install', 'TrustSpotIntegrationController@install');
            Route::get('callback', 'TrustSpotIntegrationController@callback');
        });

        // BigCommerce
        Route::group(['prefix' => 'bigcommerce'], function () {
            Route::get('install', 'BigcommerceIntegrationController@install');
            Route::get('finish', 'BigcommerceIntegrationController@connectBigcommerce');
            Route::get('load', 'BigcommerceIntegrationController@load');
            Route::get('uninstall', 'BigcommerceIntegrationController@uninstall');
            Route::get('test', 'BigcommerceIntegrationController@test');
        });
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/getCommonData', 'SparkController@getCommonData');

        Route::group([
            'prefix'    => 'webapi',
            'namespace' => 'Api',
        ], function () {
            Route::get('merchants', 'Merchant\MerchantController@index');
            Route::get('merchants/{id}', 'Merchant\MerchantController@show');
            Route::post('merchants', 'Merchant\MerchantController@store');
            Route::put('merchants/{id}', 'Merchant\MerchantController@update');
            Route::delete('merchants/{id}', 'Merchant\MerchantController@delete');
        });

        // @todo Refactor custom controller
        // Announcement
        Route::get('/announcement', 'Kiosk\AnnouncementController@get')->name('announcement');
        Route::delete('/announcement/delete/{id}', 'Kiosk\AnnouncementController@destroy')->name('announcement.delete');

        //Customer
        Route::get('/settings/customer', 'Settings\Customer\CustomerSettingController@get')->name('settings.customer');
        Route::get('/settings/customer/{customer_id}', 'Settings\Customer\CustomerSettingController@find')
            ->name('settings.customer.find');
        Route::put('/settings/customer/{customer_id}', 'Settings\Customer\CustomerSettingController@update')
            ->name('settings.customer.update');
        Route::put('/settings/customer/tier/{customer_id}', 'Settings\Customer\CustomerSettingController@updateTier')
            ->name('settings.customer.tier.update');
        Route::get('/settings/customer/{customer_id}/earning', 'Settings\Customer\CustomerSettingController@earning')
            ->name('settings.customer.earning');
        Route::get('/settings/customer/{customer_id}/spending', 'Settings\Customer\CustomerSettingController@spending')
            ->name('settings.customer.spending');
        Route::get('/settings/customer/{customer_id}/vip', 'Settings\Customer\CustomerSettingController@vipActivity')
            ->name('settings.customer.vip');
        Route::get('/settings/customer/{customer_id}/orders', 'Settings\Customer\CustomerSettingController@orders')
            ->name('settings.customer.orders');
        Route::get('/settings/customer/{customer_id}/referral-orders', 'Settings\Customer\CustomerSettingController@referralOrders')
            ->name('settings.customer.referral-orders');
        Route::post('/settings/customer/{customer}/give-reward', 'Settings\Customer\CustomerSettingController@giveReward')
            ->name('settings.customer.give-reward');
        Route::post('/settings/customer/{customer}/adjust-points', 'Settings\Customer\CustomerSettingController@adjustPoints')
            ->name('settings.customer.adjust-points');

        //export/import customer
        Route::get('/customer/export', 'Settings\Customer\ImportExportController@export')->name('customer.export');
        Route::post('/customer/import', 'Settings\Customer\ImportExportController@import')->name('customer.import');
        Route::get('/customer/template', 'Settings\Customer\ImportExportController@downloadTemplate')
            ->name('customer.template');

        // Tag
        Route::get('/settings/customer/{customer_id}/tags', 'Settings\Customer\CustomerSettingController@getTags')
            ->name('settings.customer.tags');
        Route::put('/settings/customer/{customer_id}/tags', 'Settings\Customer\CustomerSettingController@storeTags')
            ->name('settings.customer.tags');
        Route::get('/settings/tag', 'Settings\Merchant\TagSettingController@get')->name('settings.tag');

        // Display
        Route::group(['prefix' => 'display'], function () {
            Route::get('/widget/tab/get', 'Settings\Display\Widget\TabSettingsController@get')->name('widget.tab.get');
            Route::post('/widget/tab/store', 'Settings\Display\Widget\TabSettingsController@store')
                ->name('widget.tab.store');
            Route::get('/widget/widget/get', 'Settings\Display\Widget\WidgetSettingsController@get')
                ->name('widget.widget.get');
            Route::post('/widget/widget/store', 'Settings\Display\Widget\WidgetSettingsController@store')
                ->name('widget.widget.store');
            Route::get('/widget/widget/logged/get', 'Settings\Display\Widget\WidgetLoggedSettingsController@get')
                ->name('widget.widget.logged.get');
            Route::post('/widget/widget/logged/store', 'Settings\Display\Widget\WidgetLoggedSettingsController@store')
                ->name('widget.widget.logged.store');
            Route::get('/widget/branding/get', 'Settings\Display\Widget\BrandingSettingsController@get')
                ->name('widget.branding.get');
            Route::post('/widget/branding/store', 'Settings\Display\Widget\BrandingSettingsController@store')
                ->name('widget.branding.store');
        });

        // Settings
        Route::group(['prefix' => '/settings'], function () {
            // Rewards
            Route::group(['prefix' => 'rewards'], function () {
                Route::get('/', 'Settings\Merchant\MerchantRewardsController@all');
            });
            // Display
            Route::group(['prefix' => 'display'], function () {
                // Email Notifications
                Route::group(['prefix' => 'email-notifications'], function () {
                    Route::get('/settings', 'Settings\Display\EmailNotification\EmailNotificationSettingsController@getSettings');
                    Route::post('/settings', 'Settings\Display\EmailNotification\EmailNotificationSettingsController@saveSettings');
                    Route::post('/{group}/{type}/test', 'Settings\Display\EmailNotification\EmailNotificationSettingsController@test');
                    Route::get('/{group}/{type}', 'Settings\Display\EmailNotification\EmailNotificationSettingsController@get');
                    Route::post('/{group}/{type}', 'Settings\Display\EmailNotification\EmailNotificationSettingsController@store');
                });
            });

            Route::put('/point/settings', 'Settings\Point\SettingsPointsController@edit')->name('point.setting');
            Route::put('/point/reminde', 'Settings\Point\SettingsPointsController@editReminde')
                ->name('point.reminde.setting');
            Route::put('/point/final/reminde', 'Settings\Point\SettingsPointsController@updateFinalReminder')
                ->name('point.final.reminde.setting');
            Route::get('/point/data', 'Settings\Point\SettingsPointsController@get')->name('point.data');
            Route::get('/point/name', 'Settings\Point\SettingsPointsController@getName')->name('point.name');

            //referral settings
            Route::get('/referral/data', 'Settings\referrals\SettingsReferralsController@getReferral')
                ->name('point.data');
            Route::post('/referral/edit', 'Settings\referrals\SettingsReferralsController@updateReferral')
                ->name('point.setting');
        });

        //merchant
        Route::post('/settings/merchant/store', 'Settings\Merchant\InformationController@store')
            ->name('merchant.store');

        // invite Employee
        Route::post('/settings/employee/invite', 'Settings\Employee\InviteEmployeeController@invite')
            ->name('employee.invite');
        Route::put('/settings/employee/edit', 'Settings\Employee\EmployeeSettingsController@edit')
            ->name('settings.employee.edit');
        Route::delete('/settings/employee/delete/{id}', 'Settings\Employee\EmployeeSettingsController@delete')
            ->name('settings.employee.delete');

        // Payment
        Route::post('/settings/payment/shopify/delete', 'Settings\Payment\ShopifyController@store');
        Route::post('/settings/payment/stripe/subscription', 'Settings\Payment\StripeController@createSubscription');
        Route::post('/settings/payment/shopify/create', 'Settings\Payment\ShopifyController@createSubscription');
        Route::post('/settings/payment/stripe/webhook/success', 'Settings\Payment\StripeController@successfullCharge');
        Route::post('/settings/payment/stripe/webhook/failer', 'Settings\Payment\StripeController@failerCharge');
        //Route::delete('/settings/payment/delete/{merchant_name}/{id}', 'Settings\Payment\ChargeController@delete');
        Route::post('/settings/payment/stripe/invoices', 'Settings\Payment\StripeController@createInvoices');
        Route::post('/settings/payment/change/credit', 'Settings\Payment\StripeController@changeCreditCard');

        // points
        Route::get('/settings/point/{customer_id}/{merchant_id}', 'Settings\Point\AddPointController@visitLink');
        Route::get('/settings/point/{customer_id}/{merchant_id}', 'Settings\Point\AddPointController@shareSocial');
        Route::get('/settings/point/{customer_id}/{merchant_id}', 'Settings\Point\AddPointController@review');

        // import points
        Route::post('/point/import', 'Settings\Point\ImportController@import')->name('point.import');
        Route::get('/point/template', 'Settings\Point\ImportController@downloadTemplate')
            ->name('points.adjust.template');

        Route::group(['prefix' => 'points'], //        'middleware' => ['role:owner']],
            function () {

                // points overview
                Route::get('/overview', 'Settings\Point\OverviewPointsController@showPoints')->name('points.overview');

                // points earning
                Route::get('/earning', 'Settings\Point\Earning\EarningPointsController@getMerchantAction')
                    ->name('points.earning');
                Route::get('/earning/actions', 'Settings\Point\Earning\EarningPointsController@getDefaultActions')
                    ->name('points.earning.actions');
                Route::get('/earning/actions/get/{name}', 'Settings\Point\Earning\Actions\ActionController@get')
                    ->name('points.earning.actions.get');
                Route::post('/earning/actions/store', 'Settings\Point\Earning\Actions\ActionController@store')
                    ->name('points.earning.actions.store');
                Route::delete('/earning/actions/icon/{id}', 'Settings\Point\Earning\Actions\ActionController@deleteIcon')
                    ->name('points.earning.actions.icon.delete');

                //points spending
                Route::get('/spending', 'Settings\Point\Spending\SpendingPointsController@getMerchantReward')
                    ->name('points.spending');
                Route::get('/spending/reward/list', 'Settings\Point\Spending\SpendingPointsController@getReward')
                    ->name('points.spending.list');
                Route::get('/spending/rewards', 'Settings\Point\Spending\SpendingPointsController@getDefaultReward')
                    ->name('points.spending.rewards');
                Route::get('/spending/rewards/get/{name}', 'Settings\Point\Spending\Rewards\RewardController@get')
                    ->name('points.spending.rewards.get');
                Route::post('/spending/rewards/store', 'Settings\Point\Spending\Rewards\RewardController@store')
                    ->name('points.earning.spending.store');
                Route::delete('/spending/rewards/icon/{id}', 'Settings\Point\Spending\Rewards\RewardController@deleteIcon')
                    ->name('points.spending.rewards.icon.delete');
                Route::post('/spending/rewards/import-coupons/{id}', 'Settings\Point\Spending\Rewards\RewardController@importCoupons')
                    ->name('points.spending.rewards.coupons.import');

                Route::get('/spending/rewards/get-coupons/{id}', 'Settings\Point\Spending\Rewards\RewardController@getCoupons')
                    ->name('points.spending.rewards.coupons.get');

                // points activity
                Route::get('/activity/show', 'Settings\Point\ActivityController@getPointActivity')
                    ->name('points.activity.show');
                Route::post('/activity/get_data', 'Settings\Point\ActivityController@getActivity')
                    ->name('points.activity.get_data');
                Route::get('/activity/export', 'Settings\Point\ActivityController@export')
                    ->name('points.activity.export');
            });

        // invite Employee

        Route::get('/settings/employee', 'Settings\Profile\ContactInformationController@settingEmployee')
            ->name('settings.employee');
        Route::get('/settings/employee/filter', 'Settings\Profile\ContactInformationController@filter')
            ->name('settings.employee.filter');

        // Profile Contact Information...
        //Route::put('/settings/contact', 'Settings\Profile\ContactInformationController@update');

        Route::group(['prefix' => 'referrals'],

            function () {
                Route::group(['prefix' => 'rewards'], function () {

                    Route::group(['prefix' => '/receiver'], function () {
                        Route::get('/', 'Settings\referrals\rewards\RewardsReferralsController@getDefaultRewardReceiver')
                            ->name('referrals.rewards.receiver');
                        Route::post('/store', 'Settings\referrals\rewards\receiver\RewardController@store')
                            ->name('referrals.rewards.receiver.store');
                        Route::get('/get/{name}', 'Settings\referrals\rewards\receiver\RewardController@get')
                            ->name('referrals.rewards.receiver.get');
                        Route::delete('/delete/{id}', 'Settings\referrals\rewards\receiver\RewardController@deleteMerchantReward');
                        Route::delete('/icon/{id}', 'Settings\referrals\rewards\receiver\RewardController@deleteIcon');
                    });
                    Route::group(['prefix' => '/sender'], function () {
                        Route::get('/', 'Settings\referrals\rewards\RewardsReferralsController@getDefaultRewardSender')
                            ->name('referrals.rewards.sender');
                        Route::post('/store', 'Settings\referrals\rewards\sender\RewardController@store')
                            ->name('referrals.rewards.sender.store');
                        Route::get('/get/{name}', 'Settings\referrals\rewards\sender\RewardController@get')
                            ->name('referrals.rewards.sender.get');
                        Route::delete('/delete/{id}', 'Settings\referrals\rewards\sender\RewardController@deleteMerchantReward');
                        Route::delete('/icon/{id}', 'Settings\referrals\rewards\sender\RewardController@deleteIcon');
                    });

                    Route::get('/get', 'Settings\referrals\rewards\RewardsReferralsController@getMerchantReward')
                        ->name('referrals.rewards');
                });
            });

        // shopify  Connect information
        Route::post('/settings/shopify', 'Settings\Shopify\DataController@store')->name('shopify.store');
        Route::post('/settings/shopify/connect', 'Settings\Shopify\ConnectController@connect')->name('shopify.connect');
        Route::post('/settings/shopify/webhook', 'Settings\Shopify\ConnectController@storeWebhook')
            ->name('shopify.store.webhook');

        // Current Store
        Route::get('/current/merchant', 'Settings\Store\UpdateDetailController@getCurrent');
        Route::get('/current/merchant/{id}', 'Settings\Store\UpdateDetailController@currentMerchant');
        Route::get('/settings/store/show', 'Settings\Store\UpdateDetailController@showMerchant');
        Route::get('/settings/store/details', 'Settings\Store\UpdateDetailController@currentMerchantDetails');

        // Store Detail...
        Route::put('/settings/store', 'Settings\Store\UpdateDetailController@update');

        // Profile Contact Information...
        Route::get('account/settings/data', 'Settings\Profile\ContactInformationController@getSettings')
            ->name('settings');
        Route::get('account/settings', 'Settings\Profile\ContactInformationController@settings')->name('settings');

        // VIP
        Route::group([
            'prefix'     => '/vip',
            'middleware' => [
                'auth',
                'vip_permissions',
            ],
        ], function () {
            Route::group(['prefix' => '/activity'], function () {
                Route::get('/', 'Settings\Vip\ActivityController@show')->name('vip.activity');
                Route::get('/data', 'Settings\Vip\ActivityController@get')->name('vip.activity.data');
                Route::get('/export', 'Settings\Vip\ActivityController@export')->name('vip.activity.export');
            });
            Route::group(['prefix' => '/members'], function () {
                Route::get('/', 'Settings\Vip\MemberController@show')->name('vip.members');
                Route::get('/data', 'Settings\Vip\MemberController@get')->name('vip.members.data');
                Route::get('/export', 'Settings\Vip\MemberController@export')->name('vip.members.export');
            });
            Route::group(['prefix' => '/settings'], function () {
                Route::get('/', 'Settings\Vip\SettingsController@show')->name('vip.settings');
                Route::get('/data', 'Settings\Vip\SettingsController@get')->name('vip.settings.data');
                Route::post('/edit', 'Settings\Vip\SettingsController@edit')->name('vip.settings.edit');
            });
            Route::group(['prefix' => '/tiers'], function () {
                Route::get('/', 'Settings\Vip\Tier\TierController@get')->name('vip.tiers');
                Route::get('/get', 'Settings\Vip\Tier\TierController@getTiers')->name('vip.tiers.get');
                Route::get('/add', 'Settings\Vip\Tier\TierController@add')->name('vip.tiers.add');
                Route::post('/store', 'Settings\Vip\Tier\TierController@store')->name('vip.settings.store');
                Route::post('/update', 'Settings\Vip\Tier\TierController@update')->name('vip.settings.update');
                Route::get('/reward', 'Settings\Vip\Tier\TierController@getReward')->name('vip.tier.reward.data');
                Route::get('/edit/{id}', 'Settings\Vip\Tier\TierController@getEditPage')->name('vip.tiers.edit');
                Route::get('/edit/data/{id}', 'Settings\Vip\Tier\TierController@getById')->name('vip.tiers.edit.data');
                Route::get('/data', 'Settings\Vip\Tier\TierController@getData')->name('vip.tiers.data');
                Route::delete('/icon/{id}', 'Settings\Vip\Tier\TierController@deleteIcon')->name('vip.tiers.edit.icon');
            });
        });

        Route::get('/oauthCallback', 'Settings\DashboardController@oauthCallback');

        //Route::post('/settings/invitations/{invitation}/accept', 'Api\Settings\Merchants\PendingInvitationController@accept');
        //Route::post('/settings/invitations/{invitation}/reject', 'Api\Settings\Merchants\PendingInvitationController@reject');
    });
});

// Route Shopify Callback
Route::post('/getShopifyEvent', 'Settings\Shopify\ConnectController@getWebhookEvent');
Route::post('/getAppUninstalledEvent', 'Settings\Shopify\ConnectController@getWebhookAppUninstallEvent');
//Route::post('/payment/charge/accept', 'Settings\Payment\ChargeController@chargeAccept');

Route::get('notifications', function () {
    return view('notifications.index');
})->name('notifications.index');

Route::post('/integrations/webhooks/{integration}/key-verify', 'IntegrationWebhookController@verifyKey');
Route::post('/integrations/webhooks/{integration}/{webhook}', 'IntegrationWebhookController@handle');

// Payment Charge Accept
Route::get('/payment/{service}/charge/accept', 'PaymentController@accept');
Route::get('/payment/{service}/success', 'PaymentController@success');
Route::get('/payment/{service}/cancel', 'PaymentController@cancel');

Route::post('payment/webhook/stripe', 'Webhook\StripeWebhookController@handleWebhook');

Route::get('/ref/{referral_slug}', 'ReferralController@index');

// Unsubscribe from emails
Route::get('/unsubscribe', 'UnsubscribeController@store')->name('unsubscribe.store');

//Permissions and Plans
Route::group(['prefix' => 'permissions'], function () {  // check on permission for current user
    Route::get('/check/{type_code}', 'PaidPermissionsController@havePermission')->name('check-permission');

    // !!!!!!!!! ONLY FOR TEST !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    Route::get('/change-plan/{merchant_id}/{plan_id}', 'PaidPermissionsController@changePlan'); // DELETE ON PRODUCTION
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
});

Route::group(['prefix' => 'demo-data'], function () {
    Route::get('/plans', 'Api\Plan\PlanController@getPlansWithFeatures');
    Route::get('/plans/accept', 'Api\Plan\PlanController@accept');
    Route::get('/customers', 'DemoDataContoller@customers')->name('demo.data.customers');
    Route::get('/points-activity', 'DemoDataContoller@pointsActivity')->name('demo.data.points-activity');
    Route::get('/vip-activity', 'DemoDataContoller@vipActivity')->name('demo.data.vip-activity');
    Route::get('/referrals-activity', 'DemoDataContoller@referralsActivity')->name('demo.data.referrals-activity');

    Route::get('/billing', 'DemoDataContoller@billing')->name('demo.data.billing');
    Route::get('/reports-referrers', 'DemoDataContoller@reportsReferrers')->name('demo.reports.referrers');
    Route::get('/popular-earning', 'DemoDataContoller@popularEarning');
    Route::get('/popular-spending', 'DemoDataContoller@popularSpending');

    Route::get('shopify/webhook', function () {


        $shopDomain = 'lootly-alex.myshopify.com';
        $token = request('token');

        $api = ShopifyApi::setup();
        $api->setShop($shopDomain);
        $api->setAccessToken($token);

        $shop = $api->rest('GET', '/admin/shop.json', []);

        $shopWebhooks = $api->rest('GET', '/admin/webhooks.json', [
            'limit' => 250,
        ]);
        $webhooks = $shopWebhooks;

        $shopScripttags = $api->rest('GET', '/admin/script_tags.json', [
            'limit' => 250,
        ]);
        $scripttags = $shopScripttags;

        $shopMetafields = $api->rest('GET', '/admin/metafields.json', [
            'limit' => 250,
        ]);
        $metafields = $shopMetafields;

        $themes = $api->rest('GET', '/admin/themes.json', [
            'limit' => 250,
        ]);

        $mainThemeIndx = array_search('main', array_column($themes->body->themes, 'role'));

        $mainThemeId = $themes->body->themes[$mainThemeIndx]->id;

        $assets = $api->rest('GET', '/admin/themes/'.$mainThemeId.'/assets.json', [
            'fields' => 'id,key',
        ]);
    });
});
