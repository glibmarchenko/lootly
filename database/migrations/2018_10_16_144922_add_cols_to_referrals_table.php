<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColsToReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->unsignedInteger('referral_customer_id')->after('id');
            $table->unsignedInteger('invited_customer_id')->after('referral_customer_id');

            $table->foreign('referral_customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('invited_customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropForeign(['invited_customer_id']);
            $table->dropForeign(['referral_customer_id']);

            $table->dropColumn('invited_customer_id');
            $table->dropColumn('referral_customer_id');
        });
    }
}
