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

$factory->define(App\Models\TierHistory::class, function (Faker $faker) {
    return [
        'new_tier_id' => App\Models\Tier::where(['merchant_id' => 3, 'status' => 1])->inRandomOrder()->first()->id,
        'old_tier_id' => null,
    ];
});
