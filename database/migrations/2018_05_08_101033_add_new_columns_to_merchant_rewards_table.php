<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToMerchantRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->string('rewardDefaultText')->nullable()->after('reward_type');
            $table->string('coupon_expiration')->nullable()->after('coupon_prefix');
            $table->string('coupon_expiration_time')->nullable()->after('coupon_expiration');
            $table->integer('variable_reward_value')->nullable()->change();
            $table->integer('variable_point_min')->nullable()->change();
            $table->integer('variable_point_max')->nullable()->change();
            $table->string('coupon_prefix')->nullable()->change();
            $table->integer('order_minimum')->nullable()->change();
            $table->integer('category_id')->nullable()->change();
            $table->integer('product_id')->nullable()->change();
            $table->string('send_email_notification')->nullable()->change();
            $table->integer('reward_value')->nullable()->change();
            $table->string('points_required')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->dropColumn(['rewardDefaultText', 'coupon_expiration', 'coupon_expiration_time']);
        });
    }
}
