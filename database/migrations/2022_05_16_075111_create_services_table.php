<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {

            $table->bigIncrements('id_service');

             $table->string('service_name');
            //  $table->integer('operator_id');
            $table->bigInteger('operator_id')->unsigned()->nullable();
             $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')
             ->onUpdate('SET NULL');
            $table->string('operator_name');
            $table->string('dascription');
            $table->integer('service_type');
            $table->bigInteger('sdc');
            $table->float('price',30,2);
            $table->string('keyword');
            $table->smallInteger('owner');
            $table->string('keyword_complete');
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
        Schema::dropIfExists('services');
    }
}
