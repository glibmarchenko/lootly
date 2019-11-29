<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTierIdColInCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('tier_id')->unsigned()->nullable()->change();
        });

        $available_tiers = DB::table('tiers')->get()->pluck('id')->toArray();
        DB::table('customers')->whereNotIn('tier_id', $available_tiers)->update([
            'tier_id' => null
        ]);

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('tier_id')->references('id')->on('tiers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['tier_id']);
        });
    }
}
