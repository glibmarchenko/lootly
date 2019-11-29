<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_sharing', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_id');

            // Facebook
            $table->boolean('facebook_status')->default(0);
            $table->text('facebook_message')->nullable();
            $table->string('facebook_icon', 255)->nullable();
            $table->string('facebook_icon_name', 255)->nullable();

            //Twitter
            $table->boolean('twitter_status')->default(0);
            $table->text('twitter_message')->nullable();
            $table->string('twitter_icon', 255)->nullable();
            $table->string('twitter_icon_name', 255)->nullable();

            //Google
            $table->boolean('google_status')->default(0);
            $table->text('google_message')->nullable();
            $table->string('google_icon', 255)->nullable();
            $table->string('google_icon_name', 255)->nullable();

            //Email
            $table->boolean('email_status')->default(0);
            $table->text('email_subject')->nullable();
            $table->text('email_body')->nullable();

            //Share
            $table->text('share_title')->nullable();
            $table->text('share_description')->nullable();

            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_sharing');
    }
}
