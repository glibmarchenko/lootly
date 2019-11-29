<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPointsTableModifyMerchantActionAndRewardsIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->integer('merchant_action_id')->unsigned()->nullable()->change();
            $table->foreign('merchant_action_id')->references('id')->on('merchant_actions');
            $table->integer('merchant_reward_id')->unsigned()->nullable()->change();
            $table->foreign('merchant_reward_id')->references('id')->on('merchant_rewards');
            $table->integer('coupon_id')->unsigned()->nullable()->change();
            $table->foreign('coupon_id')->references('id')->on('coupons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            //
            $table->dropForeign(['merchant_action_id']);
            $table->integer('merchant_action_id')->change();
            $table->dropForeign(['merchant_reward_id']);
            $table->integer('merchant_reward_id')->change();
            $table->integer('coupon_id');
            $table->integer('coupon_id')->change();
        });
    }
}
