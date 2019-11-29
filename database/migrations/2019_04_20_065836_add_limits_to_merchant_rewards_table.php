<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLimitsToMerchantRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->boolean('spending_limit')->nullable()->after('order_minimum');
            $table->integer('spending_limit_value')->nullable()->after('spending_limit');
            $table->string('spending_limit_type')->nullable()->after('spending_limit_value');
            $table->string('spending_limit_period')->nullable()->after('spending_limit_type');
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
            $table->dropColumn(['spending_limit', 'spending_limit_value', 'spending_limit_type', 'spending_limit_period']);
        });
    }
}
