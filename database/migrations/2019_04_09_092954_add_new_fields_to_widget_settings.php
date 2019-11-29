<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToWidgetSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $table->text('widget_logged_points_reedem_tab_text')->nullable()->after('widget_welcome_points_rewards_earning_title');
            $table->text('widget_logged_points_rewards_tab_button')->nullable()->after('widget_logged_points_reedem_tab_text');
            $table->text('widget_logged_points_earn_tab_text')->nullable()->after('widget_logged_points_rewards_tab_button');
            $table->text('widget_logged_points_earn_tab_button')->nullable()->after('widget_logged_points_earn_tab_text');
            $table->text('widget_logged_my_rewards_title')->nullable()->after('widget_logged_points_earn_tab_button');
            $table->text('widget_logged_my_rewards_text')->nullable()->after('widget_logged_my_rewards_title');
            $table->text('widget_logged_no_rewards_text')->nullable()->after('widget_logged_my_rewards_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
