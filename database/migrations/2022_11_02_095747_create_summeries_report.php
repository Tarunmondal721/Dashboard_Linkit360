<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummeriesReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


         Schema::create('report_summarize', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('operator_id');
            $table->bigInteger('operator_id')->unsigned()->nullable();
            $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->string('operator_name');
            // $table->integer('country_id');
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL')->onUpdate('SET NULL');

            $table->date('date');
            $table->unique(['operator_id','date']);
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
        Schema::dropIfExists('report_summarize');
    }
}
