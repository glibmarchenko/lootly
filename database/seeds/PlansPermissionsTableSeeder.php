<?php

use Illuminate\Database\Seeder;

class PlansPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $growthPlan = [
            [
                'plan_id' => 2,
                'paid_permission_id' => 1, // Read Content
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 2, // TrustSpot Review
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 3, //Email Customization
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 4, //Referral Program
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 5, //Import Existing Customers
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 6, //Integrations
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 7, //Rewards Link
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 8, //Customer Segmentation
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 9, //Earning Limits
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 10, //Remove Lootly Branding
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 11, //Remove Lootly Branding for Email
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 12, //Email Earning Customization
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 13, //Email Spending Customization
            ],
            [
                'plan_id' => 2,
                'paid_permission_id' => 27, //Earning Limits
            ],
        ];

        $unlimatePlan = [
            [
                'plan_id' => 3,
                'paid_permission_id' => 14, //Employee Access
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 15, //Advanced Customization
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 16, //Advanced Earning Customization
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 17, //Advanced Spending Customization
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 18, //Advanced Referral Customization
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 19, //Advanced Tab Customization
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 20, //VIP Program
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 21, //Rewards Page
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 22, //Variable Discount Coupons
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 23, //Insights & Reports
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 24, //HTML Editor
            ],
            [
                'plan_id' => 3,
                'paid_permission_id' => 25, //Points Expiration
            ],
        ];
        $unlimatePlan = array_merge($unlimatePlan, $this->changePlanId($growthPlan, 3));

        $enteprisePlan = [
            [
                'plan_id' => 4,
                'paid_permission_id' => 26, //Custom Domain
            ],
        ];
        $enteprisePlan = array_merge($enteprisePlan, $this->changePlanId($unlimatePlan, 4));

        \DB::table('plans_permissions')->delete();
        \DB::table('plans_permissions')->insert(array_merge($growthPlan, $unlimatePlan, $enteprisePlan));
    }

    protected function changePlanId(array $array,int $planId){
        // print_r($array);
        $newArray = [];
        foreach($array as $elem){
            $elem['plan_id'] = $planId;
            array_push($newArray, $elem);
        }
        return $newArray;
    }
}