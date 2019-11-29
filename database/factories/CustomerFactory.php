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

$factory->define(App\Models\Customer::class, function (Faker $faker) {
    return [
        'merchant_id' => 1,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'country' => $faker->country,
        'zipcode' => $faker->postcode,
        'birthday' => $faker->date('Y-m-d', '2000-07-06'),
        'referral_slug' => substr($faker->unique()->md5, 0, 10),
        'tier_id' => App\Models\Tier::where(['merchant_id' => 3, 'status' => 1])->inRandomOrder()->first()->id,
        'ecommerce_id' => substr($faker->unique()->md5, 0, 10),
    ];
});
