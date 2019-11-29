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

$factory->define(App\Models\Point::class, function (Faker $faker) {
    return [
        'merchant_id' => 3,
        'customer_id' => null,
        'point_value' => '',
        'merchant_action_id' => null,
        'merchant_reward_id' => null,
        'coupon_id' => null,
        'order_id' => null,
        'total_order_amount' => '',
        'rewardable_order_amount' => '',
        'type' => '',
        'expiration_date' => '',
        'tier_multiplier' => '1',
        'referral_id' => '',
    ];
});
