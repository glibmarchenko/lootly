<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOrderIdColInPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->integer('order_id')->nullable()->unsigned()->change();
        });

        $available_orders = DB::table('orders')->get()->pluck('id')->toArray();
        DB::table('points')->whereNotIn('order_id', $available_orders)->update([
            'order_id' => null
        ]);

        Schema::table('points', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
}
