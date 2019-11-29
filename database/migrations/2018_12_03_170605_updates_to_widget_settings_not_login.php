<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatesToWidgetSettingsNotLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {

            // Not Login Mode
            $table->dropColumn('widget_overview_position');
            $table->dropColumn('widget_overview_text');
            $table->string('widget_points_rewards_text', 300)->nullable()->after('widget_welcome_background_opacity');
            $table->string('widget_ways_to_earn_text', 300)->nullable()->after('widget_points_rewards_text');
            $table->string('widget_ways_to_earn_position')->nullable()->after('widget_ways_to_earn_text');
            $table->string('widget_ways_to_spend_text', 300)->nullable()->after('widget_ways_to_earn_position');
            $table->string('widget_ways_to_spend_position')->nullable()->after('widget_ways_to_spend_text');

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
