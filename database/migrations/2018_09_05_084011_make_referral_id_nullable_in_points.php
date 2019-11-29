<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeReferralIdNullableInPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->integer('referral_id')->unsigned()->nullable()->change();
        });

        $available_referrals = DB::table('customers')->get()->pluck('id')->toArray();
        DB::table('points')->whereNotIn('referral_id', $available_referrals)->update([
            'referral_id' => null
        ]);

        Schema::table('points', function (Blueprint $table) {
            $table->foreign('referral_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['referral_id']);
        });

        DB::table('points')->whereNull('referral_id')->update([
            'referral_id' => ''
        ]);

        Schema::table('points', function (Blueprint $table) {
            $table->string('referral_id')->nullable(false)->change();
        });
    }
}
