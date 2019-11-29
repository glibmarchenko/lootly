<?php

use Illuminate\Database\Seeder;

class RewardTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reward_arr = [
            [
                'name' => 'Fixed amount discount',
                'slug' => 'fixed-amount',
                'display_text' => '$ off',
                'icon' => 'icon-coin',
                'type' => 'Fixed amount',
                'url' => 'fixed-discount.get',
                'display_order' => 1
            ],
            [
                'name' => 'Variable amount discount',
                'slug' => 'variable-amount',
                'display_text' => '$ off',
                'icon' => 'icon-coin',
                'type' => 'Variable amount',
                'url' => 'variable-discount.get',
                'display_order' => 2
            ],
            [
                'name' => 'Percentage off discount',
                'slug' => 'percentage-off',
                'display_text' => '% off',
                'icon' => 'icon-percentage',
                'type' => 'Percentage off',
                'url' => 'percentage-discount.get',
                'display_order' => 3
            ],
            [
                'name' => 'Free shipping discount',
                'slug' => 'free-shipping',
                'display_text' => 'Free Shipping',
                'icon' => 'icon-package',
                'type' => 'Free shipping',
                'url' => 'free-shipping.get',
                'display_order' => 4
            ],
            [
                'name' => 'Free product discount',
                'slug' => 'free-product',
                'display_text' => 'Free Product',
                'icon' => 'icon-gift',
                'type' => 'Free Product',
                'url' => 'free-product.get',
                'display_order' => 5
            ],
            [
                'name' => 'Points',
                'slug' => 'points',
                'display_text' => 'Points',
                'icon' => 'icon-points',
                'type' => 'Points',
                'url' => 'points.get',
                'display_order' => null
            ],
        ];
        DB::table('rewards')->delete();
        DB::table('rewards')->insert($reward_arr);
    }
}
