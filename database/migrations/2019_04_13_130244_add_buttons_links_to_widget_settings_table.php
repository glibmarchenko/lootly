<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddButtonsLinksToWidgetSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            $table->string('widget_welcome_signup_link')->nullable()->after('widget_welcome_login_link_text');
            $table->string('widget_welcome_login_link')->nullable()->after('widget_welcome_signup_link');
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
            $table->dropColumn('widget_welcome_signup_link');
            $table->dropColumn('widget_welcome_login_link');
        });
    }
}
