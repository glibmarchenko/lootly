<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersSeeder::class);
        $this->call(ActionTableSeed::class);
        $this->call(RewardTableSeed::class);
        $this->call(SubscriptionPlanTableSeed::class);
        $this->call(CurrencyTableSeed::class);
        $this->call(CurrencySignsSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(EmailNotificationTableSeed::class);
        $this->call(UserNotificationTypeTableSeed::class);
        $this->call(IntegrationTableSeed::class);
        $this->call(PlansTableSeeder::class);
        $this->call(PaidPermitionsTableSeeder::class);
        $this->call(PlansPermissionsTableSeeder::class);
        // $this->call(OrdersTableSeeder::class);
    }
}
