<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnActionIconName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->string('reward_icon_name')->nullable()->after('reward_icon');


        });
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->string('action_icon_name')->nullable()->after('action_icon');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->dropColumn(['reward_icon_name']);
        });
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->dropColumn(['action_icon_name']);
        });
    }
}
