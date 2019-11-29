<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTeamUsersTableName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('merchant_user', function (Blueprint $table) {
            $table->integer('merchant_id')->unsigned()->nullable(false);
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->integer('user_id')->unsigned()->nullable(false);
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('role', 20);
            $table->timestamps();
            $table->unique(['merchant_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('merchnat_user');
    }
}
