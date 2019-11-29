<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultNameToPointSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('point_settings', function ($table) {
            $table->string('name')->default('point')->change();
            $table->string('plural_name')->default('points')->change();
        });

        \DB::statement('UPDATE point_settings set name="point" WHERE name=""');
        \DB::statement('UPDATE point_settings set plural_name="points" WHERE plural_name=""');
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
