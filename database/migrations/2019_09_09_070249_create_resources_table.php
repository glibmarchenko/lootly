<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('author_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();

            $table->string('title');
            $table->string('slug');

            $table->longText('body')->nullable();
            $table->mediumText('description')->nullable();
            $table->text('meta_description')->nullable();

            $table->string('mini_image')->nullable();
            $table->string('featured_image')->nullable();

            $table->foreign('author_id')->references('id')->on('authors');
            $table->foreign('category_id')->references('id')->on('categories');

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
        Schema::dropIfExists('resources');
    }
}
