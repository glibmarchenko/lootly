<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Assign Merchant/Team to user.
        // \DB::insert("INSERT INTO `merchant_users` (`user_id`, `merchant_id`, `role`) VALUES ('1', '1', 'owner')");

        // Add orders
        // \DB::table('customers')->delete();
        // \DB::insert("INSERT INTO `customers` (`id`, `merchant_id`, `name`, `email`, `country`, `zipcode`, `birthday`, `referral_slug`, `tier_id`, `ecommerce_id`, `created_at`, `updated_at`) VALUES ('1', '1', 'Rayan', 'test0@mail.com', 'USA', '1', '0000-00-00', 'test0', NULL, NULL, '2018-10-03 00:00:00', '2018-10-09 00:00:00')");
        // \DB::insert("INSERT INTO `customers` (`id`, `merchant_id`, `name`, `email`, `country`, `zipcode`, `birthday`, `referral_slug`, `tier_id`, `ecommerce_id`, `created_at`, `updated_at`) VALUES ('2', '1', 'Larry', 'test1@mail.com', 'USA', '2', '0000-00-00', 'test1', NULL, NULL, '2018-10-03 00:00:00', '2018-10-09 00:00:00')");
        // \DB::insert("INSERT INTO `customers` (`id`, `merchant_id`, `name`, `email`, `country`, `zipcode`, `birthday`, `referral_slug`, `tier_id`, `ecommerce_id`, `created_at`, `updated_at`) VALUES ('3', '1', 'Ahmed', 'test2@mail.com', 'USA', '3', '0000-00-00', 'test2', NULL, NULL, '2018-10-03 00:00:00', '2018-10-09 00:00:00')");

        // \DB::table('orders')->delete();
        // \DB::insert("INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `referral_slug`, `referring_customer_id`, `total_price`, `refunded_total`, `coupon_id`, `status`, `created_at`, `updated_at`) VALUES (NULL, '0', '1', 'ewqr', '3', '123', '123.00', NULL, NULL, '2018-10-03 00:00:00', '2018-10-03 00:00:00')");
        // \DB::insert("INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `referral_slug`, `referring_customer_id`, `total_price`, `refunded_total`, `coupon_id`, `status`, `created_at`, `updated_at`) VALUES (NULL, '0', '2', 'ewqr', '1', '123', '123.00', NULL, NULL, '2015-10-03 00:00:00', '2018-10-03 00:00:00')");
        // \DB::insert("INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `referral_slug`, `referring_customer_id`, `total_price`, `refunded_total`, `coupon_id`, `status`, `created_at`, `updated_at`) VALUES (NULL, '0', '3', 'ewqr', '2', '123', '123.00', NULL, NULL, '2018-07-03 00:00:00', '2018-10-03 00:00:00')");

    }
}
