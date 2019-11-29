<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTierBenefits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tier_benefits', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('tier_id');
        $table->integer('merchant_reward_id')->nullable();
        $table->string('benefits_type')->nullable();
        $table->string('benefits_reward')->nullable();
        $table->string('benefits_discount')->nullable();
        $table->timestamps();
    });
        Schema::table('tiers', function (Blueprint $table) {
            $table->dropColumn(['benefits_reward', 'benefits_discount', 'benefits_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tier_benefits');
    }
}
