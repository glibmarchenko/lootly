<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id')->nullable();
            // $table->unsignedInteger('faq_settings_id');
            // $table->unsignedInteger('earn_settings_id');
            // $table->unsignedInteger('header_settings_id');
            // $table->unsignedInteger('how_it_works_id');
            // $table->unsignedInteger('refferal_settings_id');
            // $table->unsignedInteger('spending_settings_id');
            // $table->unsignedInteger('vip_settings_id');

            // $table->foreign('faq_settings_id')->references('id')->on('faq_settings')->onDelete('cascade');
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
        Schema::dropIfExists('reward_settings');        
    }
}
