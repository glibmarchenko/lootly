<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    use App\Traits\TraitSeederHelper;

    protected $seedsNum = 10000;

    protected $numOfSpendings = 20;

    protected $couponId = 0;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $randArray = [0, 1];
        $subscriptions = [
            [
                'name' => 'test subscription',
                'merchant_id' => 1,
                'plan_id' => 1,
                'length' => 'month',
                'ends_at' => Carbon::now()->addMonth(),
            ],
        ];

        // \DB::table('subscriptions')->delete();
        // \DB::table('subscriptions')->insert($subscriptions);
        $this->insertArray('subscriptions', $subscriptions);

        $customers = [];

        for($i = 0; $i < $this->seedsNum; $i++){
            $customers[] = [
                'id' => $i + 1,
                'merchant_id' => 1,
                'name' => $faker->name,
                'email' => $faker->email,
                'country' => $faker->country,
                'zipcode' => $faker->postcode,
                'birthday' => $faker->dateTimeThisCentury(),
                'referral_slug' => $faker->sha1,
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 months', 'now'),
            ];
        }

        // \DB::table('customers')->delete();
        // \DB::table('customers')->insert($customers);
        $this->insertArray('customers', $customers);

        $merchant_action = [
            'id' => 1,
            'action_id' => 1,
            'merchant_id' => 1,
            'action_name' => 'Make a Purchase',
            'action_icon_name' => 'icon-cart',
            'earning_limit_period' => 'lifetime',
            'reward_text' => '123 Points per $1 spent',
            'reward_default_text' => null,
            'point_value' => 312,
            'goal' => 123,
            'earning_limit' => 0,
            'earning_limit_type' => 0,
            'reward_email_text' => 'You just earned {points} {points-name} at {company-name} for making a purchase with us.',
            'default_email_text' => 'You just earned {points} {points-name} at {company-name} for making a purchase with us.',
            'is_fixed' => 'variable-amount',
            'send_email_notification' => 1,
            'active_flag' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        \DB::statement('DELETE FROM `merchant_actions` WHERE `id` = 1');
        \DB::table('merchant_actions')->insert($merchant_action);

        $merchant_reward = [
            'id' => 1,
            'reward_id' => 1,
            'merchant_id' => 1,
            'type_id' => 1,
            'reward_type' => 'Fixed amount',
            'rewardDefaultText' => '{points} {points-name}',
            'reward_text' => '123 Points',
            'reward_name' => '€21 off discount',
            'rewardDefaultName' => '{reward-value} off discount {min-value}',
            'reward_icon_name' => 'icon-coin',
            'reward_email_text' => "You just redeemed {points} {points-name} for a {reward-name} at {company}.\nBelow is your coupon code to use on your next order.",
            'points_required' => 123,
            'reward_value' => 21,
            'coupon_expiration_time' => 'days',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        \DB::statement('DELETE FROM `merchant_rewards` WHERE `id` = 1');
        \DB::table('merchant_rewards')->insert($merchant_reward);

        \DB::table('coupons')->delete();
        \DB::table('points')->delete();
        // \DB::table('coupons')->insert($coupons);
        // create orders and actions
        $orders = [];
        for($i = 1; $i <= $this->seedsNum * 2; $i++){
            $isCoupon = array_rand($randArray);
            $customerId = $faker->numberBetween(1, $this->seedsNum);
            $createdDate = $faker->dateTimeBetween('-2 months', 'now');
            if(!$isCoupon){ //create activity for earning action
                $this->createActivity($customerId, $this->couponId, 'Earning', $createdDate);
            } else {    // create coupon and activity for spending action
                $this->createCoupon($customerId, $createdDate);
                $this->createActivity($customerId, $this->couponId, 'Spending', $createdDate);
            }
            
            $orders[] = [
                'id' => $i,
                'order_id' => $faker->md5,
                'customer_id' => $customerId,
                'referral_slug' => $isCoupon ? null : $faker->sha1,
                'referring_customer_id' =>  $isCoupon ? null : $faker->numberBetween(1, $this->seedsNum),
                'total_price' => $faker->numberBetween(10, 1000),
                'refunded_total' => $faker->numberBetween(10, 100),
                'coupon_id' => $isCoupon ? $this->couponId : null,
                'created_at'=> $createdDate,
            ];
        }
        // \DB::table('orders')->delete();
        // \DB::table('orders')->insert($orders);
        $this->insertArray('orders', $orders);

        for($i = 1; $i <= $this->seedsNum; $i++){
            app('customer_service')->updateTier(1, $i);
        }

        $billings = [
            [
                'user_id' => 1,
                'merchant_id' => 1,
                'plan_id' => 3,
                'name' => 'Growth',
                'price' => 49,
                'period' => 'Monthly',
                'date' => $faker->dateTimeBetween('-1 months', 'now'),
            ],
            [
                'user_id' => 1,
                'merchant_id' => 1,
                'plan_id' => 2,
                'name' => 'Startup',
                'price' => 108,
                'period' => 'Yearly',
                'date' => $faker->dateTimeBetween('-1 months', 'now'),
            ],
        ];

        \DB::table('billings')->delete();
        \DB::table('billings')->insert($billings);
        
        $destinations = ['email', 'twitter', 'facebook'];

        $shares = [];
        for($i = 1; $i < $this->seedsNum; $i++){
            $shares[] = [
                'customer_id' => $faker->numberBetween(1, $this->seedsNum),
                'shared_to' => $destinations[array_rand($destinations)],
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
            ];
        }
        // \DB::table('customer_referral_shares')->delete();
        // \DB::table('customer_referral_shares')->insert($shares);
        $this->insertArray('customer_referral_shares', $shares);

        $clicks = [];
        for($i = 1; $i < $this->seedsNum; $i++){
            $clicks[] = [
                'customer_id' => $faker->numberBetween(1, $this->seedsNum),
                'clicked_from' => $destinations[array_rand($destinations)],
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
            ];
        }
        // \DB::table('customer_referral_clicks')->delete();
        // \DB::table('customer_referral_clicks')->insert($clicks);
        $this->insertArray('customer_referral_clicks', $clicks);
    }
}
