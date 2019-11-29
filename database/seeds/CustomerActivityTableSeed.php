<?php

use Illuminate\Database\Seeder;

class CustomerActivityTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $merchant_id = 3;

        factory(App\Models\Customer::class, 10)->create([
            'merchant_id' => $merchant_id,
        ])->each(function ($u) use ($merchant_id) {

            // Tier History
            $getTiers = App\Models\Tier::where('id', '!=', $u->tier_id)->where([
                'merchant_id' => $merchant_id,
                'status'      => 1,
            ])->pluck('id')->toArray();

            if (count($getTiers)) {
                $times = rand(1, count($getTiers));

                $previous_tier = null;
                $previous_tier_date = date('Y-m-d');
                for ($i = 0; $i < $times; $i++) {
                    if ($i == 0) {
                        $new_tier = $u->tier_id;
                    } else {
                        if ($previous_tier) {
                            $new_tier = $previous_tier;
                        } else {
                            $k = array_rand($getTiers);
                            $new_tier = $previous_tier = array_slice($getTiers, $k, 1)[0];
                        }
                    }

                    if ($i == $times - 1) {
                        $old_tier = null;
                    } else {
                        $k = array_rand($getTiers);
                        $old_tier = $previous_tier = array_slice($getTiers, $k, 1)[0];
                    }

                    $u->tier_history()->save(factory(App\Models\TierHistory::class)->make([
                        'new_tier_id' => $new_tier,
                        'old_tier_id' => $old_tier,
                        'customer_id' => $u->id,
                        'created_at'  => \Carbon\Carbon::createFromFormat('Y-m-d', $previous_tier_date),
                        'updated_at'  => \Carbon\Carbon::createFromFormat('Y-m-d', $previous_tier_date),
                    ]));

                    $previous_tier_date = date('Y-m-d', strtotime($previous_tier_date.' -1 day'));
                }
            }

            // Points History

            $earned_points = 0;

            $actions = App\Models\MerchantAction::get()->toArray();
            if (count($actions)) {
                $earningCount = rand(0, 15);
                for ($i = 0; $i < $earningCount; $i++) {
                    $k = array_rand($actions);
                    $u->points()->save(factory(App\Models\Point::class)->make([
                        'merchant_id'             => $merchant_id,
                        'customer_id'             => $u->id,
                        'point_value'             => $actions[$k]['point_value'],
                        'merchant_action_id'      => $actions[$k]['id'],
                        'merchant_reward_id'      => null,
                        'coupon_id'               => null,
                        'order_id'                => 'ORDERID',
                        'total_order_amount'      => 0,
                        'rewardable_order_amount' => 0,
                        'type'                    => '',
                        'expiration_date'         => '',
                        'tier_multiplier'         => '1',
                        'referral_id'             => '',
                    ]));
                    $earned_points += $actions[$k]['point_value'];
                }
            }

            $rewards = App\Models\MerchantReward::where('points_required', '<=', $earned_points)->get()->toArray();
            if (count($rewards)) {
                $spendingCount = rand(0, 15);
                for ($i = 0; $i < $spendingCount; $i++) {
                    $k = array_rand($rewards);
                    $earned_points -= $rewards[$k]['points_required'];
                    if ($earned_points < 0) {
                        break;
                    }
                    $u->points()->save(factory(App\Models\Point::class)->make([
                        'merchant_id'             => $merchant_id,
                        'customer_id'             => $u->id,
                        'point_value'             => '-'.$rewards[$k]['points_required'],
                        'merchant_action_id'      => null,
                        'merchant_reward_id'      => $rewards[$k]['id'],
                        'coupon_id'               => factory(App\Models\Coupon::class)->create([
                            'merchant_id'        => $merchant_id,
                            'customer_id'        => $u->id,
                            'merchant_reward_id' => $rewards[$k]['id'],
                            'is_used'            => 0,
                        ])->id,
                        'order_id'                => 'ORDERID',
                        'total_order_amount'      => 0,
                        'rewardable_order_amount' => 0,
                        'type'                    => '',
                        'expiration_date'         => '',
                        'tier_multiplier'         => '1',
                        'referral_id'             => '',
                    ]));
                }
            }

            // Order History

            $ordersCount = rand(0, 30);
            for ($i = 0; $i < $ordersCount; $i++) {
                $referrer = App\Models\Customer::where('id', '!=', $u->id)->inRandomOrder()->first();

                $coupon = factory(App\Models\Coupon::class)->create([
                    'merchant_id'        => $merchant_id,
                    'customer_id'        => $u->id,
                    'merchant_reward_id' => App\Models\MerchantReward::where(['merchant_id' => $merchant_id])
                        ->first()->id,
                    'is_used'            => 1,
                ]);

                $u->orders()->save(factory(App\Models\Order::class)->make([
                    'user_id'               => 3,
                    'referral_slug'         => $referrer->referral_slug ?: 'qwerty',
                    'referring_customer_id' => $referrer->id,
                    'coupon_id'             => $coupon->id,
                    'order_id'              => rand(1000, 9999999)
                ]));
            }
        });
    }
}
