<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionToScServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sc_services', function (Blueprint $table) {
            //
            $table->longText('subscription_keyword')->nullable();
            $table->longText('unsubscription_keyword')->nullable();
            $table->longText('portal_information')->nullable();
            $table->string('subs_sms')->nullable();
            $table->string('unsubs_sms')->nullable();
            $table->string('renewal_sms')->nullable();
            $table->string('campaign_type')->nullable();
            $table->string('campaign_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sc_services', function (Blueprint $table) {
            //
        });
    }
}
