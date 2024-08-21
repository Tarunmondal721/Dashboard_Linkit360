<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_checklists', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('service_id')->unsigned()->nullable();
            $table->foreign('service_id')->references('id')->on('sc_services')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->unique('service_id');
            $table->string('pmo_1')->default('no');
            $table->string('pmo_2')->default('no');
            $table->string('pmo_3')->default('no');
            $table->string('pmo_4')->default('no');
            $table->string('pmo_5')->default('no');
            $table->string('pmo_6')->default('no');
            $table->string('pmo_7')->default('no');
            $table->string('pmo_8')->default('no');
            $table->string('pmo_9')->default('no');
            $table->string('pmo_10')->default('no');
            $table->string('pmo_11')->default('no');
            $table->string('pmo_12')->default('no');
            $table->string('pmo_13')->default('no');
            $table->string('pmo_14')->default('no');
            $table->string('pmo_15')->default('no');
            $table->string('pmo_16')->default('no');
            $table->string('pmo_17')->default('no');
            $table->string('pmo_18')->default('no');
            $table->string('pmo_19')->default('no');
            $table->string('infra_1')->default('no');
            $table->string('infra_2')->default('no');
            $table->string('infra_3')->default('no');
            $table->string('infra_4')->default('no');
            $table->string('infra_5')->default('no');
            $table->string('infra_6')->default('no');
            $table->string('infra_7')->default('no');
            $table->string('business_1')->default('no');
            $table->string('business_2')->default('no');
            $table->string('business_3')->default('no');
            $table->string('business_4')->default('no');
            $table->string('business_5')->default('no');
            $table->string('business_6')->default('no');
            $table->string('cs_1')->default('no');
            $table->string('cs_2')->default('no');
            $table->string('cs_3')->default('no');
            $table->string('cs_4')->default('no');
            $table->string('cs_5')->default('no');
            $table->string('finance_1')->default('no');
            $table->string('finance_2')->default('no');
            $table->string('finance_3')->default('no');
            $table->string('finance_4')->default('no');
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
        Schema::dropIfExists('service_checklists');
    }
}
