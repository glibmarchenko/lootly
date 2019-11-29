<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceCaseStudiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_case_studies', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('resource_id')->unique();

            $table->string('industry')->nullable();
            $table->string('platform')->nullable();
            $table->string('favorite_feature')->nullable();

            $table->string('stat_first_title')->nullable();
            $table->string('stat_first_value')->nullable();

            $table->string('stat_second_title')->nullable();
            $table->string('stat_second_value')->nullable();

            $table->string('stat_third_title')->nullable();
            $table->string('stat_third_value')->nullable();

            $table->mediumText('top_quote')->nullable();

            $table->string('customer_name')->nullable();
            $table->string('position_title')->nullable();

            $table->mediumText('company_body')->nullable();
            $table->string('company_image')->nullable();

            $table->mediumText('challenge_body')->nullable();
            $table->mediumText('challenge_quote')->nullable();

            $table->mediumText('solution_body')->nullable();
            $table->mediumText('solution_quote')->nullable();
            $table->string('solution_image')->nullable();

            $table->mediumText('results_body')->nullable();
            $table->string('results_image')->nullable();

            $table->foreign('resource_id')->references('id')->on('resources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_case_studies');
    }
}
