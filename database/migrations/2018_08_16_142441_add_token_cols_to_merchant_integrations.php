<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenColsToMerchantIntegrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_integrations', function (Blueprint $table) {
            $table->string('token', 512)->nullable()->after('status');
            $table->string('refresh_token', 512)->nullable()->after('token');
            $table->timestamp('expires_at')->nullable()->after('refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_integrations', function (Blueprint $table) {
            $table->dropColumn('expires_at');
            $table->dropColumn('refresh_token');
            $table->dropColumn('token');
        });
    }
}
