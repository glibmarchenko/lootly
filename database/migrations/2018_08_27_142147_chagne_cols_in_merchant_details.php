<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChagneColsInMerchantDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_details', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('merchant_details');
            if ($doctrineTable->hasIndex('merchant_details_user_id_foreign')) {
                $table->dropForeign(['user_id']);
                //$table->dropIndex('merchant_details_user_id_foreign');
            }
            if ($doctrineTable->hasColumn('hmac')) {
                $table->dropColumn('hmac');
            }
            if ($doctrineTable->hasColumn('token')) {
                $table->dropColumn('token');
            }
            if ($doctrineTable->hasColumn('shop_domain')) {
                $table->dropColumn('shop_domain');
            }
            $table->renameColumn('shopify_api_key', 'api_key');
            $table->renameColumn('shopify_api_secret', 'api_secret');
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
        Schema::table('merchant_details', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('merchant_details');
            if ($doctrineTable->hasIndex('merchant_details_merchant_id_foreign')) {
                $table->dropForeign(['merchant_id']);
            }
            //$table->index('merchant_id', 'merchant_details_user_id_foreign');
            $table->renameColumn('api_secret', 'shopify_api_secret');
            $table->renameColumn('api_key', 'shopify_api_key');
            $table->string('shop_domain')->after('merchant_id');
            $table->string('token')->nullable()->after('shop_domain');
            $table->string('hmac')->nullable()->after('token');
        });
    }
}
