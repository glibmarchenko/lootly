<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ForeignRelationInPointsTableChanged extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['merchant_action_id']);
            $table->dropForeign(['merchant_reward_id']);
            $table->dropForeign(['coupon_id']);

            $table->foreign('merchant_action_id')->references('id')->on('merchant_actions')->onDelete('set null');
            $table->foreign('merchant_reward_id')->references('id')->on('merchant_rewards')->onDelete('set null');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('set null');
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
            $table->dropForeign(['merchant_action_id']);
            $table->dropForeign(['merchant_reward_id']);
            $table->dropForeign(['coupon_id']);

            $table->foreign('merchant_action_id')->references('id')->on('merchant_actions');
            $table->foreign('merchant_reward_id')->references('id')->on('merchant_rewards');
            $table->foreign('coupon_id')->references('id')->on('coupons');
        });
    }
}
