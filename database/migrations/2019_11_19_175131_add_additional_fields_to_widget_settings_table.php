<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsToWidgetSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $table->text('widget_logged_points_needed_text')->nullable()->after('widget_logged_points_rewards_button_text');
            $table->text('widget_logged_points_activity_title')->nullable()->after('widget_logged_points_needed_text');
            $table->text('custom_css')->nullable()->after('brand_remove_in_widget');
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
            $table->dropColumn('widget_logged_points_needed_text');
            $table->dropColumn('widget_logged_points_activity_title');
            $table->dropColumn('custom_css');
        });
    }
}
