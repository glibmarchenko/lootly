<?php

use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'id' => 1,
                'name' => 'Free',
                'type' => 'free',
                'price' => 0,
                'growth_order' => 0,
            ],
            [
                'id' => 2,
                'name' => 'Growth',
                'type' => 'growth',
                'price' => 49,
                'growth_order' => 1,
            ],
            [
                'id' => 3,
                'name' => 'Ultimate',
                'type' => 'ultimate',
                'price' => 249,
                'growth_order' => 2,
            ],
            [
                'id' => 4,
                'name' => 'Enterprise',
                'type' => 'enterprise',
                'price' => 599,
                'growth_order' => 3,
            ],
        ];
        \DB::table('plans')->delete();
        \DB::query("DBCC CHECKIDENT('plans', RESEED, 0)"); //reset table id counter
        \DB::table('plans')->insert($plans);
    }
}
