<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyReportSummeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('report_summarize_monthly')) {
            Schema::create('report_summarize_monthly', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('country_id')->unsigned()->nullable();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->year('year');
                $table->string('month');
                $table->string('key');
                $table->bigInteger('operator_id')->unsigned()->nullable();
                $table->unique(['operator_id','user_id','year','month']);
                $table->integer('fmt_success')->default(0);;
                $table->integer('fmt_failed')->default(0);;
                $table->integer('mt_success')->default(0);;
                $table->integer('mt_failed')->default(0);;
                $table->decimal('gros_rev',30,2);
                $table->integer('total_reg');
                $table->integer('total')->default(0);
                $table->integer('total_unreg')->default(0);;
                $table->integer('purge_total')->default(0);;
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
        Schema::dropIfExists('monthly_report_summeries');
    }
}
