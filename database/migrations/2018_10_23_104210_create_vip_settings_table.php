<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVipSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vip_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reward_settings_id')->nullable();
            $table->string('title')->nullable();
            $table->string('multiplier_color')->nullable();
            $table->integer('multiplier_font_size')->nullable();
            $table->string('requirements_color')->nullable();
            $table->integer('requirements_font_size')->nullable();
            $table->string('tier_name_color')->nullable();
            $table->integer('tier_name_font_size')->nullable();
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
        Schema::dropIfExists('vip_settings');
    }
}
