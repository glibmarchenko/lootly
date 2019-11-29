<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHowItWorksToWidgetSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $table->string('widget_ways_to_earn_title')->nullable()->after('widget_ways_to_earn_text');
            $table->string('widget_ways_to_spend_title')->nullable()->after('widget_ways_to_earn_position');
            $table->string('widget_how_it_works_title')->nullable()->after('widget_logged_referral_background_opacity');
            $table->string('widget_how_it_works_text')->nullable()->after('widget_how_it_works_title');
            $table->string('widget_how_it_works_position')->nullable()->after('widget_how_it_works_text');
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
