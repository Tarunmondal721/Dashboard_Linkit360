<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsToolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('cs_tools')) {
            Schema::create('cs_tools', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('operator_id')->unsigned()->nullable();
                // $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
                $table->string('operator_name');
                $table->string('source');
                $table->integer('msisdn');
                $table->string('country_name');
                $table->bigInteger('country_id')->unsigned()->nullable();
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL')->onUpdate('SET NULL');
                $table->string('service');
                $table->string('type');
                $table->string('status');
                $table->string('cycle');
                $table->string('adnet');
                $table->dateTime('freemium_end_date');
                $table->decimal('revenue',30,4)->nullable();
                $table->dateTime('subs_date');
                $table->dateTime('renewal_date');
                $table->dateTime('schedule_charge');
                $table->dateTime('last_charge_attempt'); 
                $table->string('unsubs_from');
                $table->string('subs_from');
                $table->integer('service_price');
                $table->integer('profile_status');
                $table->string('publisher');
                $table->string('handset');
                $table->string('browser');
                $table->string('trxid');
                $table->string('pixel');
                $table->string('telco_api_url');
                $table->string('telco_api_response');
                $table->dateTime('telco_api_hit_date');
                $table->dateTime('sms_send_date');
                $table->string('sms_content');
                $table->string('status_sms');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cs_tools');
    }
}
