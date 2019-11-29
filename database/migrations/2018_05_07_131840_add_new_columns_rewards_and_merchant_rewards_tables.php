<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsRewardsAndMerchantRewardsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->string('reward_name')->nullable()->after('reward_type');
            $table->string('rewardDefaultName')->nullable()->after('reward_name');
            $table->string('reward_icon')->nullable()->after('rewardDefaultName');
            $table->string('reward_email_text')->nullable()->after('reward_icon');
        });
        Schema::table('rewards', function (Blueprint $table) {
            $table->string('url')->nullable()->after('name');
            $table->string('type')->nullable()->after('url');
            $table->string('description')->nullable()->after('type');
            $table->integer('display_order')->nullable()->after('description');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->dropColumn(['rewardDefaultName', 'reward_icon', 'reward_email_text']);
        });
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn(['url', 'type', 'description', 'display_order']);
        });
    }
}
