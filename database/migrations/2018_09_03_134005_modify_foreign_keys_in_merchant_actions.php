<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyForeignKeysInMerchantActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropForeign(['action_id']);
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('action_id')->references('id')->on('actions')->onDelete('cascade');
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
            $table->dropForeign(['merchant_id']);
            $table->dropForeign(['action_id']);
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('action_id')->references('id')->on('actions');
        });
    }
}
