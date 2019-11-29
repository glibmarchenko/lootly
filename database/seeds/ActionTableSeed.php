<?php

use Illuminate\Database\Seeder;

class ActionTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $action_arr = [
            // Order
            [
                'name' => 'Make a Purchase',
                'icon' => 'icon-cart',
                'type' => 'Orders',
                'description' => 'These actions are around customer\'s placing orders on your store.',
                'url' => 'make-a-purchase',
                'action_btn_text' => null,
                'display_order' => 1,
                'priority' => 10,
            ],
            [
                'name' => 'Goal Spend',
                'icon' => 'icon-goal-spend',
                'type' => 'Orders',
                'description' => 'These actions are around customer\'s placing orders on your store.',
                'url' => 'goal-spend',
                'action_btn_text' => null,
                'display_order' => 2,
                'priority' => null,

            ],
            [
                'name' => 'Goal Orders',
                'icon' => 'icon-goal-orders',
                'type' => 'Orders',
                'description' => 'These actions are around customer\'s placing orders on your store.',
                'url' => 'goal-orders',
                'action_btn_text' => null,
                'display_order' => 3,
                'priority' => null,
            ],


            //Account
            [
                'name' => 'Create an Account',
                'icon' => 'icon-create-account',
                'type' => 'Account',
                'description' => 'These actions are around the customer\'s account with your store.',
                'url' => 'create-account',
                'action_btn_text' => null,
                'display_order' => 4,
                'priority' => null,
            ],
            [
                'name' => 'Celebrate a Birthday',
                'icon' => 'icon-birthday',
                'type' => 'Account',
                'description' => 'These actions are around the customer\'s account with your store.',
                'url' => 'celebrate-birthday',
                'action_btn_text' => 'Enter date',
                'display_order' => 5,
                'priority' => null,
            ],
            // Social
            [
                'name' => 'Facebook Like',
                'icon' => 'icon-facebook',
                'type' => 'Social',
                'description' => 'Allow your customers to earn points for liking or sharing your brand on social media.',
                'url' => 'facebook-like',
                'action_btn_text' => 'Like us',
                'display_order' => 6,
                'priority' => null,
            ],
            [
                'name' => 'Facebook Share',
                'icon' => 'icon-facebook',
                'type' => 'Social',
                'description' => 'Allow your customers to earn points for liking or sharing your brand on social media.',
                'url' => 'facebook-share',
                'action_btn_text' => 'Share',
                'display_order' => 7,
                'priority' => null,
            ],


            [
                'name' => 'Twitter Follow',
                'icon' => 'icon-twitter',
                'type' => 'Social',
                'description' => 'Allow your customers to earn points for liking or sharing your brand on social media.',
                'url' => 'twitter-follow',
                'action_btn_text' => 'Follow us',
                'display_order' => 8,
                'priority' => null,
            ],
            [
                'name' => 'Twitter Share',
                'icon' => 'icon-twitter',
                'type' => 'Social',
                'description' => 'Allow your customers to earn points for liking or sharing your brand on social media.',
                'url' => 'twitter-share',
                'action_btn_text' => 'Share',
                'display_order' => 9,
                'priority' => null,
            ],
            [
                'name' => 'Instagram Follow',
                'icon' => 'icon-instagram',
                'type' => 'Social',
                'description' => 'Allow your customers to earn points for liking or sharing your brand on social media.',
                'url' => 'instagram-follow',
                'action_btn_text' => 'Follow us',
                'display_order' => 10,
                'priority' => null,
            ],
            //Store
            [
                'name' => 'TrustSpot - Reviews & UGC',
                'icon' => 'icon-star',
                'type' => 'Store',
                'description' => 'Reward your customers for interacting with your site content.',
                'url' => 'trustspot-review',
                'action_btn_text' => null,
                'display_order' => 12,
                'priority' => null,
            ],
            [
                'name' => 'Read Content',
                'icon' => 'icon-content',
                'type' => 'Store',
                'description' => 'Reward your customers for interacting with your site content.',
                'url' => 'read-content',
                'action_btn_text' => 'View Link',
                'display_order' => 11,
                'priority' => null,
            ],
            //Custom
            [
                'name' => 'Custom Zapier Action',
                'icon' => 'icon-custom',
                'type' => 'Custom',
                'description' => 'Create custom earning actions with Zapier.',
                'url' => 'custom-earning',
                'action_btn_text' => null,
                'display_order' => 13,
                'priority' => null,
            ],
        ];
        DB::table('actions')->delete();
        DB::table('actions')->insert($action_arr);
    }
}
