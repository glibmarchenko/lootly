<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InvitationsRevertingChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('invitations');
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('team_id')->index();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('role')->nullable();
            $table->string('status');
            $table->string('email');
            $table->string('token', 40)->unique();
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
        Schema::drop('invitations');
        Schema::create('invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('merchant_id')->index();
            $table->string('access')->nullable();
            $table->string('status');
            $table->string('email');
            $table->string('token', 40)->unique();
            $table->timestamps();
        });
    }
}
