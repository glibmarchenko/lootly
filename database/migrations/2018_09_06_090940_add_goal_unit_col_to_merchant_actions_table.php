<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoalUnitColToMerchantActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->string('goal_unit')->nullable()->after('goal');
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
            $table->dropColumn('goal_unit');
        });
    }
}
