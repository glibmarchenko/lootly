<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToRewardCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reward_coupons', function (Blueprint $table) {
            $table->index(['merchant_reward_id']);
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
        Schema::table('reward_coupons', function (Blueprint $table) {
            $table->dropForeign(['merchant_reward_id']);
            $table->dropIndex(['merchant_reward_id']);
        });
    }
}
