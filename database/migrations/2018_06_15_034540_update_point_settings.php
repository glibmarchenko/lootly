<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePointSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('point_settings', function ($table) {
            $table->string('name')->default('Point')->change();
            $table->string('plural_name')->default('Points')->change();
        });

        \DB::statement('UPDATE point_settings set name="Point" WHERE name="point"');
        \DB::statement('UPDATE point_settings set plural_name="Points" WHERE plural_name="points"');
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
