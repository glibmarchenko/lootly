<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->unsigned()->nullable(false);
            $table->integer('customer_id')->unsigned()->nullable(false);
            $table->integer('point_value');
            $table->integer('merchant_action_id');
            $table->integer('merchant_reward_id');
            $table->integer('coupon_id');
            $table->string('order_id');
            $table->integer('total_order_amount');
            $table->integer('rewardable_order_amount');
            $table->string('type')->comment("Earned, Redeemed, API, Manual, Etc");
            $table->dateTime('expiration_date');
            $table->integer('tier_multiplier')->default(1);
            $table->string('referral_id');
            $table->timestamps();
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('points');
    }
}
