<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconcialiationMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconcialiation_media', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->integer('operator_id')->nullable();
            $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->integer('year');
            $table->integer('month');
            $table->date('date');
            $table->unique(['operator_id','date']);
            $table->double('cost_campaign')->default(0.0000);
            $table->double('mo')->default(0.0000);
            $table->double('price_mo')->default(0.0000);
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
        Schema::dropIfExists('reconcialiation_media');
    }
}
