<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeEarningLimitColumnsInMerchantActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->dropColumn('earning_limit_time');
            $table->string('earning_limit_period')->nullable()->after('earning_limit');
            $table->string('earning_limit_type')->nullable()->after('earning_limit');
            $table->integer('earning_limit_value')->nullable()->after('earning_limit');
            $table->boolean('earning_limit')->nullable()->default(0)->change();
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
            $table->dropColumn('earning_limit_value');
            $table->dropColumn('earning_limit_type');
            $table->dropColumn('earning_limit_period');
            $table->string('earning_limit')->nullable()->change();
            $table->string('earning_limit_time')->nullable()->after('earning_limit');
        });
    }
}
