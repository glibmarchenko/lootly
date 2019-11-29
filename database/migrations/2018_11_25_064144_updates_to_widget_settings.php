<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatesToWidgetSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widget_settings', function (Blueprint $table) {
            // Tab Design Updates
            $table->dropColumn('tab_hide_on_mobile');
            $table->integer('tab_side_spacing')->nullable()->after('tab_font_color');
            $table->integer('tab_bottom_spacing')->nullable()->after('tab_side_spacing');
            $table->string('tab_display_on')->nullable()->after('tab_bottom_spacing'); // desktop - mobile - none
            $table->string('tab_desktop_layout')->nullable()->after('tab_display_on'); // icon-text / icon / text
            $table->boolean('tab_custom_icon')->default(0)->after('tab_desktop_layout');
           
            // Widget Design Updates
            $table->renameColumn('widget_welcome_text', 'widget_welcome_title');
            $table->string('widget_welcome_subtitle', 300)->nullable()->after('widget_welcome_text');
            $table->string('widget_welcome_header_title')->nullable()->after('widget_welcome_subtitle');
            $table->string('widget_welcome_header_subtitle')->nullable()->after('widget_welcome_header_title');
            $table->string('widget_welcome_login')->nullable()->after('widget_welcome_header_subtitle');
          
            // Branding Design Updates
            $table->dropColumn('brand_font_color');
            $table->string('brand_header_bg')->nullable()->after('brand_secondary_color');
            $table->string('brand_header_bg_font_color')->nullable()->after('brand_header_bg');
            $table->string('brand_button_color')->nullable()->after('brand_header_bg_font_color');
            $table->string('brand_button_font_color')->nullable()->after('brand_button_color');

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
