<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnToMerchantActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->string('action_name')->after('merchant_id');
            $table->string('action_icon')->nullable()->after('action_name');
            $table->string('reward_text')->after('action_icon');
            $table->string('earning_limit_time')->after('action_icon');
            $table->string('reward_email_text')->after('earning_limit');
            $table->integer('point_value')->nullable()->change();
            $table->string('option_1')->nullable()->change();
            $table->string('option_2')->nullable()->change();
            $table->integer('earning_limit')->nullable()->change();
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
            $table->dropColumn(['action_name', 'action_icon', 'rewardDefaultText', 'reward_email_text']);
        });
    }
}
