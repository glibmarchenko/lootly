<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('widget_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants');

            // Tab Design Settings
            $table->boolean('tab_rewards_visible')->default(1);
            $table->string('tab_position')->nullable();
            $table->boolean('tab_hide_on_mobile')->default(0);
            $table->string('tab_text', 300)->nullable();
            $table->string('tab_bg_color')->nullable();
            $table->string('tab_font_color')->nullable();
            $table->string('tab_icon')->nullable();
            $table->string('tab_icon_name')->nullable();

            // Widget Design Settings
            $table->string('widget_welcome_text', 300)->nullable();
            $table->string('widget_welcome_position')->nullable();
            $table->string('widget_welcome_button_text')->nullable();
            $table->string('widget_welcome_background')->nullable();
            $table->string('widget_welcome_background_name')->nullable();
            $table->string('widget_welcome_background_opacity')->nullable();
            $table->string('widget_overview_text', 300)->nullable();
            $table->string('widget_overview_position')->nullable();
            $table->string('widget_rr_text', 300)->nullable();
            $table->string('widget_rr_button_text')->nullable();
            $table->string('widget_rr_background')->nullable();
            $table->string('widget_rr_background_name')->nullable();
            $table->string('widget_rr_background_opacity')->nullable();

            $table->string('widget_logged_welcome_text', 300)->nullable();
            $table->string('widget_logged_welcome_position')->nullable();
            $table->string('widget_logged_welcome_icon')->nullable();
            $table->string('widget_logged_welcome_icon_name')->nullable();
            $table->string('widget_logged_welcome_background')->nullable();
            $table->string('widget_logged_welcome_background_name')->nullable();
            $table->string('widget_logged_welcome_background_opacity')->nullable();

            $table->string('widget_logged_points_balance_text', 300)->nullable();
            $table->string('widget_logged_points_available_text', 300)->nullable();
            $table->string('widget_logged_points_earn_button_text')->nullable();
            $table->string('widget_logged_points_spend_button_text')->nullable();
            $table->string('widget_logged_points_rewards_button_text')->nullable();

            $table->string('widget_logged_vip_button_text')->nullable();
            $table->string('widget_logged_vip_background')->nullable();
            $table->string('widget_logged_vip_background_name')->nullable();
            $table->string('widget_logged_vip_background_opacity')->nullable();

            $table->string('widget_logged_referral_main_text', 300)->nullable();
            $table->string('widget_logged_referral_receiver_text', 300)->nullable();
            $table->string('widget_logged_referral_sender_text', 300)->nullable();
            $table->string('widget_logged_referral_link_text', 300)->nullable();
            $table->string('widget_logged_referral_background')->nullable();
            $table->string('widget_logged_referral_background_name')->nullable();
            $table->string('widget_logged_referral_background_opacity')->nullable();


            // Branding Design Settings
            $table->string('brand_primary_color')->nullable();
            $table->string('brand_secondary_color')->nullable();
            $table->string('brand_font_color')->nullable();
            $table->string('brand_link_color')->nullable();
            $table->string('brand_font')->nullable();
            $table->boolean('brand_remove_in_widget')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('widget_settings');
    }
}
