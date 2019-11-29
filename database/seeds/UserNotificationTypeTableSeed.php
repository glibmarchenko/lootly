<?php

use Illuminate\Database\Seeder;

class UserNotificationTypeTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notification_types_arr = [
            // Customer earned points
            [
                'slug' => 'customer_earned_points',
                'title' => 'Customer earned points',
                'description' => '',
                'status' => 1,
                'active_by_default' => 1,
            ],
            // Customer spent points
            [
                'slug' => 'customer_spent_points',
                'title' => 'Customer spent points',
                'description' => '',
                'status' => 1,
                'active_by_default' => 1,
            ],
            // Daily Program Summary
            [
                'slug' => 'daily_program_summary',
                'title' => 'Daily Program Summary',
                'description' => '',
                'status' => 1,
                'active_by_default' => 1,
            ],
            // Weekly Program Summary
            [
                'slug' => 'weekly_program_summary',
                'title' => 'Weekly Program Summary',
                'description' => '',
                'status' => 1,
                'active_by_default' => 1,
            ],
            // No Reward Codes available
            [
                'slug' => 'no_reward_codes_available',
                'title' => 'No Reward Codes available',
                'description' => '',
                'status' => 1,
                'active_by_default' => 1,
            ],
        ];
        DB::table('user_notification_types')->delete();
        DB::table('user_notification_types')->insert($notification_types_arr);
    }
}
