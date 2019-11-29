<?php

namespace App\Providers;

use App\User;
use App\Models\Order;
use App\Models\Point;
use App\Models\Customer;
use App\Observers\UserObserver;
use App\Observers\OrderObserver;
use App\Observers\PointObserver;
use App\Observers\CustomerObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        //Listen models events
        Point::observe(PointObserver::class);
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
        Customer::observe(CustomerObserver::class);

        if(env('SSL', false)) {
            \URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('postmark_api', 'App\Helpers\PostmarkApi');
        App::bind('user_service', 'App\Helpers\UserService');
    }
}
