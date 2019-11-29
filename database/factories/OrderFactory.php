<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Order::class, function (Faker $faker) {
    return [
        'user_id' => 3,
        'order_id' => substr($faker->unique()->md5, 0, 10),
        'total_price' => $faker->randomFloat(2, 1, 1000),
        'referral_slug' => null,
        'referring_customer_id' => null,
        'coupon_id' => null,
    ];
});
