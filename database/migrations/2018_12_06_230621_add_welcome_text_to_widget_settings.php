<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWelcomeTextToWidgetSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {

            $table->renameColumn('widget_points_rewards_text', 'widget_welcome_points_rewards_title');
            $table->string('widget_welcome_points_rewards_subtitle')->nullable()->after('widget_points_rewards_text');
            $table->string('widget_welcome_vip_title')->nullable()->after('widget_welcome_points_rewards_subtitle');
            $table->string('widget_welcome_vip_subtitle')->nullable()->after('widget_welcome_vip_title');
            $table->string('widget_welcome_referral_title')->nullable()->after('widget_welcome_vip_subtitle');
            $table->string('widget_welcome_referral_subtitle')->nullable()->after('widget_welcome_referral_title');

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
