<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRestrictionsEnabledToMerchantActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->boolean('restrictions_enabled')->default(0)->after('reward_id');
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
            $table->dropColumn('restrictions_enabled');
        });
    }
}
