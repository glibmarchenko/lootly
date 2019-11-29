<?php

use Illuminate\Database\Seeder;

class EmailNotificationTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notifications_arr = [
            // Points Earned
            [
                'alias' => 'points_earned',
                'group' => 'points',
                'type' => 'earned',
                'default_subject' => 'You just received points from {company-name}',
                'default_button_text' => 'Earn More Points',
                'default_button_color' => '#022c82',
            ],
            // Points Earned
            [
                'alias' => 'points_spent',
                'group' => 'points',
                'type' => 'spent',
                'default_subject' => 'You just unlocked a new reward at {company}',
                'default_button_text' => 'Shop Now',
                'default_button_color' => '#022c82',
            ],
            // Points Reward Available
            [
                'alias' => 'points_reward_available',
                'group' => 'points',
                'type' => 'reward_available',
                'default_subject' => 'Get your {reward-name} reward now at {company}',
                'default_button_text' => 'Get My Coupon',
                'default_button_color' => '#022c82',
            ],
            // Points Point Expiration
            [
                'alias' => 'points_point_expiration',
                'group' => 'points',
                'type' => 'point_expiration',
                'default_subject' => '{customer} you have reward {point-name} expiring soon at {company}',
                'default_button_text' => 'Use My Points',
                'default_button_color' => '#022c82',
            ],
            // Points VIP Tier Earned
            [
                'alias' => 'points_vip_tier_earned',
                'group' => 'points',
                'type' => 'vip_tier_earned',
                'default_subject' => 'You just unlocked the {tier-name} VIP Tier at {company}',
                'default_button_text' => 'Shop Now',
                'default_button_color' => '#022c82',
            ],
            // Referral Share Email
            [
                'alias' => 'referral_share_email',
                'group' => 'referral',
                'type' => 'share_email',
                'default_subject' => '{referral-name} just sent you a {reward-name} at {company}',
                'default_button_text' => 'Get My Coupon',
                'default_button_color' => '#022c82',
            ],
            // Referral Receiver Reward
            [
                'alias' => 'referral_receiver_reward',
                'group' => 'referral',
                'type' => 'receiver_reward',
                'default_subject' => 'You just unlocked a {reward-name} at {company}',
                'default_button_text' => 'Shop Now',
                'default_button_color' => '#022c82',
            ],
            // Referral Sender Reward
            [
                'alias' => 'referral_sender_reward',
                'group' => 'referral',
                'type' => 'sender_reward',
                'default_subject' => 'You just earned {reward-name} at {company} for a referral',
                'default_button_text' => 'Shop Now',
                'default_button_color' => '#022c82',
            ],
        ];
        DB::table('email_notifications')->delete();
        DB::table('email_notifications')->insert($notifications_arr);
    }
}
