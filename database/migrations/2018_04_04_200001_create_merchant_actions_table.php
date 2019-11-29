<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_actions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('action_id')->unsigned()->nullable(false);
            $table->integer('merchant_id')->unsigned()->nullable(false);
            $table->integer('point_value');
            $table->string('option_1');
            $table->string('option_2');
            $table->integer('reward_id');
            $table->integer('earning_limit');
            $table->boolean('is_fixed')->default(0)->comment("Only for orders: If fixed, amount is flat, not based on order $ amount");
            $table->boolean('send_email_notification')->default(0);
            $table->boolean('active_flag')->default(0);
            $table->timestamps();
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('action_id')->references('id')->on('actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_actions');
    }
}
