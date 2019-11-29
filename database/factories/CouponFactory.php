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

$factory->define(App\Models\Coupon::class, function (Faker $faker) {
    return [
        'merchant_id' => null,
        'customer_id' => null,
        'merchant_reward_id' => null,
        'shop_coupon_id' => substr($faker->md5, 0, 10),
        'coupon_code' => substr($faker->unique()->md5, 0, 16),
        'is_used' => 0,
    ];
});
