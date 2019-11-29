<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralDisplaySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_display_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reward_settings_id')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('subtitle_color')->nullable();
            $table->integer('subtitle_font_size')->nullable();
            $table->string('title_color')->nullable();
            $table->integer('title_font_size')->nullable();
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
        Schema::dropIfExists('refferal_settings');
    }
}
