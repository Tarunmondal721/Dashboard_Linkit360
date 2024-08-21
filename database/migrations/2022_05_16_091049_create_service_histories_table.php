<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('operator_id');
            $table->bigInteger('operator_id')->unsigned()->nullable();
            $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->string('operator_name');
            // $table->integer('id_service');
            $table->bigInteger('id_service')->unsigned()->nullable();
            $table->foreign('id_service')->references('id_service')->on('services')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->date('date');
            $table->unique(['id_service','date']);
            $table->integer('fmt_success');
            $table->integer('fmt_failed');
            $table->integer('mt_success');
            $table->integer('mt_failed');
            $table->decimal('gros_rev',30,2);
            $table->integer('total_reg');
            $table->integer('total_unreg');
            $table->integer('total');
            $table->integer('purge_total');
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
        Schema::dropIfExists('service_histories');
    }
}
