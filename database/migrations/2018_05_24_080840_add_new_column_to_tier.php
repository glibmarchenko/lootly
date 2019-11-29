<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnToTier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tiers', function (Blueprint $table) {
            $table->boolean('status')->nullable()->after('name');
            $table->string('text_email')->nullable()->after('status');
            $table->string('text_email_default')->nullable()->after('text_email');
            $table->string('requirement_text')->nullable()->after('text_email_default');
            $table->string('requirement_text_default')->nullable()->after('requirement_text');
            $table->string('multiplier_text')->nullable()->after('requirement_text_default');
            $table->string('multiplier_text_default')->nullable()->after('multiplier_text');
            $table->boolean('email_notification')->nullable()->after('multiplier_text_default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tiers', function (Blueprint $table) {
            $table->dropColumn(['text_email', 'requirement_text', 'multiplier_text', 'status']);
        });
    }
}
