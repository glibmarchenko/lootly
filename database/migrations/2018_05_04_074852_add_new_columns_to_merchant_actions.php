<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToMerchantActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->string('fb_page_url')->nullable()->after('point_value');
            $table->string('share_url')->nullable()->after('fb_page_url');
            $table->string('share_message')->nullable()->after('share_url');
            $table->string('twitter_username')->nullable()->after('share_message');
            $table->string('content_url')->nullable()->after('twitter_username');
            $table->string('review_type')->nullable()->after('content_url');
            $table->string('review_status')->nullable()->after('review_type');
            $table->string('earning_limit_time')->nullable()->change();
            $table->string('reward_text')->nullable()->change();
            $table->string('reward_default_text')->nullable()->after('reward_text');
            $table->string('point_value')->nullable()->change();
            $table->string('goal')->nullable()->change();
            $table->string('option_1')->nullable()->change();
            $table->string('option_2')->nullable()->change();
            $table->string('reward_id')->nullable()->change();
            $table->string('earning_limit')->nullable()->change();
            $table->string('default_email_text')->nullable()->after('reward_email_text');
            $table->string('is_fixed')->nullable()->change();
            $table->string('send_email_notification')->nullable()->change();
            $table->string('active_flag')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_actions', function (Blueprint $table) {
            $table->dropColumn(['fb_page_url', 'share_url', 'share_message', 'twitter_username', 'content_url', 'review_type', 'review_status']);
        });
    }
}
