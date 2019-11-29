<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpendingSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spending_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('reward_settings_id')->nullable();
            $table->string('title')->nullable();
            $table->string('box_color')->nullable();
            $table->integer('box_font_size')->nullable();
            $table->string('box_text_color')->nullable();
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
        Schema::dropIfExists('spending_settings');
    }
}
