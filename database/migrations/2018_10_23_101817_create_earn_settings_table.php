<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEarnSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('earn_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reward_settings_id')->nullable();
            $table->string('title')->nullable();
            $table->integer('action_font_size')->nullable();
            $table->string('action_text_color')->nullable();
            $table->string('box_color')->nullable();
            $table->string('point_color')->nullable();
            $table->integer('point_font_size')->nullable();
            $table->string('ribbon_color')->nullable();
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
        Schema::dropIfExists('earn_settings');
    }
}
