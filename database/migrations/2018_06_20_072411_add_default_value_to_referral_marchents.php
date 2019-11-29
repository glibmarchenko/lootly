<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultValueToReferralMarchents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchants', function ($table) {
            $table->string('language')->default('English')->change();
            $table->string('currency')->default('USD')->change();

            \App\Merchant::query()->whereNull('language')->update([
                'language'=>'English',
            ]);
            \App\Merchant::query()->whereNull('currency')->update([
                'currency'=>'USD',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
