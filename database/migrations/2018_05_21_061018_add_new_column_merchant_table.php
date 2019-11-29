<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnMerchantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->boolean('customer_earned_point_notification')->nullable()->default(0)->after('notification');
            $table->boolean('customer_spent_point_notification')->nullable()->default(0)->after('customer_earned_point_notification');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn(['customer-earned-point_notification', 'customer-earned-point_notification']);
        });
    }
}
