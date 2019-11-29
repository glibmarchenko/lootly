<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCouponFieldsToWidgetSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $table->text('widget_logged_reward_view_button')->nullable()->after('widget_logged_my_rewards_text');
            $table->text('widget_logged_coupon_title')->nullable()->after('widget_how_it_works_position');
            $table->text('widget_logged_coupon_copy_button')->nullable()->after('widget_logged_coupon_title');
            $table->text('widget_logged_coupon_body_text')->nullable()->after('widget_logged_coupon_copy_button');
            $table->text('widget_logged_coupon_button_text')->nullable()->after('widget_logged_coupon_body_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $table->dropColumn('widget_logged_reward_view_button');
            $table->dropColumn('widget_logged_coupon_title');
            $table->dropColumn('widget_logged_coupon_copy_button');
            $table->dropColumn('widget_logged_coupon_body_text');
            $table->dropColumn('widget_logged_coupon_button_text');
        });
    }
}
