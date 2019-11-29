<?php

use Illuminate\Database\Seeder;

class SubscriptionPlanTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plan_arr = [
            [
                'name' => 'Base',
                'price' => '500',
                'trial_days' => '5',
                'interval' => 'month',
                'currency' => 'USD',
            ],
            [
                'name' => 'Premium',
                'price' => '800',
                'trial_days' => '5',
                'interval' => 'month',
                'currency' => 'USD',
            ],
            [
                'name' => 'Base',
                'price' => '1000',
                'trial_days' => '5',
                'interval' => 'month',
                'currency' => 'USD',
            ],

        ];
        $product = new \App\Services\Stripe();
        $merchants = $this->getMerchants();
        foreach ($merchants as $merchantObj) {
            $productObj = $product->createProducts($merchantObj);
            $this->createStripePlan($plan_arr, $productObj);
        }

    }

    public function getMerchants()
    {
        $merchant = new \App\Repositories\MerchantRepository();
        return $merchant->get();
    }

    public function createStripePlan($plan_arr, $productObj)
    {
        foreach ($plan_arr as $plan) {
            $plan_db = \App\Models\SubscriptionPlan::query()->create($plan);
            try {

                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $plan_stripe = \Stripe\Plan::create([
                    'product' => $productObj->id,
                    'interval' => $plan['interval'],
                    'currency' => $plan['currency'],
                    'amount' => $plan['price'],
                ]);


                \App\Models\SubscriptionPlan::query()->where('id', '=', $plan_db->id)->update([
                    'stripe_plan_id' => $plan_stripe->id
                ]);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::info('Error');
            }

        }
    }

}
