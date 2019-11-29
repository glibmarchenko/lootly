<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpendingSettingsToMerchantRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->string('spending_settings_id')->nullable()->after('merchant_id');
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
            $table->dropColumn('spending_settings_id');
        });
    }
}
