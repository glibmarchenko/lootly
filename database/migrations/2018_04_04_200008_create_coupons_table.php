<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->unsigned()->nullable(false);
            $table->integer('customer_id')->unsigned()->nullable(false);
            $table->integer('merchant_reward_id')->unsigned();
            $table->integer('shop_coupon_id')->comment("Cart system internal ID, Ex: shopifys internal coupon id");
            $table->string('coupon_code');
            $table->boolean('is_used');

            $table->timestamps();
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('merchant_reward_id')->references('id')->on('merchant_rewards');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
