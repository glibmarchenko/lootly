<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_rewards', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('reward_id')->unsigned()->nullable(false);
            $table->integer('merchant_id')->unsigned()->nullable(false);
            $table->boolean('reward_type')->default(0)->comment("0 = point based, 1 = direct coupon");
            $table->integer('points_required');
            $table->integer('reward_value');
            $table->integer('variable_reward_value');
            $table->integer('variable_point_cost');
            $table->integer('variable_point_min');
            $table->integer('variable_point_max');
            $table->string('coupon_prefix');
            $table->integer('order_minimum');
            $table->integer('category_id'); // if only 1 category
            $table->integer('product_id'); // if only 1 product
            $table->boolean('send_email_notification')->default(0);
            $table->boolean('active_flag')->default(0);
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('reward_id')->references('id')->on('rewards');
            $table->softDeletes();
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
        Schema::dropIfExists('merchant_rewards');
    }
}
