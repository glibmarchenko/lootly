<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMerchantsAndMerchantDetailsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('merchants', function (Blueprint $table) {
            $table->string('website')->after('name');
        });
        Schema::table('merchant_details', function (Blueprint $table) {
            $table->renameColumn('user_id', 'merchant_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_details', function (Blueprint $table) {
            $table->renameColumn('merchant_id', 'user_id');
        });
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['website']);
        });
    }
}
