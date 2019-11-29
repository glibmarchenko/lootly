<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaidPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paid_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type_code')->unique();
            $table->string('upsell_title')->nullable();
            $table->string('upsell_image')->nullable();
            $table->string('upsell_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paid_permissions');
    }
}
