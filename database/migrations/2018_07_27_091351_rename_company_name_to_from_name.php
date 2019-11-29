<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCompanyNameToFromName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_email_notification_settings', function (Blueprint $table) {
            $table->renameColumn('company_name', 'from_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_email_notification_settings', function (Blueprint $table) {
            $table->renameColumn('from_name', 'company_name');
        });
    }
}
