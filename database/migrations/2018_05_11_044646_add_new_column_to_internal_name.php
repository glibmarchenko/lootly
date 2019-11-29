<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnToInternalName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_rewards', function (Blueprint $table) {
            $table->string('reward_type')->nullable()->change();
            $table->string('max_shiping')->nullable()->after('variable_point_max');


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
            $table->dropColumn(['min_shiping']);
        });
    }
}
