<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToWidgetSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $table->string('widget_welcome_login_link_text')->nullable()->after('widget_welcome_login');

            $table->string('widget_welcome_points_rewards_earning_title')->nullable()->after('widget_welcome_points_rewards_subtitle');
            $table->string('widget_welcome_points_rewards_spending_title')->nullable()->after('widget_welcome_points_rewards_earning_title');

            $table->string('widget_logged_referral_copy_button')->nullable()->after('widget_logged_referral_sender_text');
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
            //
        });
    }
}
