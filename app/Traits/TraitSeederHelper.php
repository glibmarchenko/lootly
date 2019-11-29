<?php

namespace App\Traits;

trait TraitSeederHelper {
      protected function createCoupon($customerId, $createdDate){
         $faker = \Faker\Factory::create();
         \DB::table('coupons')->insert([
            'id' => ++$this->couponId,
            'merchant_id' => 1,
            'customer_id' => $customerId,
            'merchant_reward_id' => 1,
            'shop_coupon_id' => 1,
            'coupon_code' => $faker->sha1,
            'is_used' => 1,
            'created_at' => $createdDate,
         ]);
   }

   protected function createActivity($customerId, $couponId, $type, $createdDate){
         $faker = \Faker\Factory::create();
         \DB::table('points')->insert([
            'merchant_id' => 1,
            'customer_id' => $customerId,
            'point_value' => $type == 'Earning' ? $faker->numberBetween(10, 1000) : $faker->numberBetween(-1000, -10),
            'rollback' => 0,
            'title' => $faker->word,
            'reason' => $faker->word,
            'total_order_amount' => $faker->numberBetween(10, 1000),
            'rewardable_order_amount' => $faker->numberBetween(10, 100),
            'merchant_action_id' => $type == 'Earning' ? 1 : null,
            'merchant_reward_id' => $type == 'Earning' ? null : 1,
            'coupon_id' => $type == 'Earning' ? null : $couponId,
            'tier_multiplier' => $type == 'Earning' ? null : $faker->numberBetween(1, 10),
            'created_at' => $createdDate,
         ]);
   }

   /**
      * Delete $table then fills $table with given $array
      * @param string name of table
      * @param array data array
      * 
      * @return bool true on success and false on fail
      */
   protected function insertArray(string $table, array $data){
      try{
         \DB::table($table)->delete();
         foreach (array_chunk($data, 1000) as $piece) {
            \DB::table($table)->insert($piece);
         }
         return true;
      }
      catch(\Exeption $e){
         \Log::error($e);
         return false;
      }
   }
}