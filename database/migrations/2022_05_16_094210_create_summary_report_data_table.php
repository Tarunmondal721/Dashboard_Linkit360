<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummaryReportDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_report_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('operator_id');
            $table->bigInteger('operator_id')->unsigned()->nullable();
            $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->integer('fmt_success');
            $table->integer('fmt_failed');
            $table->integer('mt_success');
            $table->integer('mt_failed');
            $table->decimal('gross_revenue',30,2);
            $table->integer('reg');
            $table->integer('unreg');
            $table->integer('total');
            $table->integer('purge');
            $table->date('date');
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
        Schema::dropIfExists('summary_report_data');
    }
}
