<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHowItWorksSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('how_it_works_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reward_settings_id')->nullable();
            $table->string('title')->nullable();
            $table->string('steep1_text')->nullable();
            $table->string('steep2_text')->nullable();
            $table->string('steep3_text')->nullable();
            $table->string('arrows_color')->nullable();
            $table->string('circle_empty_color')->nullable();
            $table->string('circle_full_color')->nullable();
            $table->integer('steps_front_size')->nullable();
            $table->string('steps_color')->nullable();
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
        Schema::dropIfExists('how_it_works_settings');
    }
}
