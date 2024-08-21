<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinalCostReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('final_cost_reports')) {
            Schema::create('final_cost_reports', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('country_id')->unsigned()->nullable();
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL')->onUpdate('SET NULL');
                $table->bigInteger('operator_id')->unsigned()->nullable();
                $table->integer('year');
                $table->integer('month');
                $table->string('key');
                $table->unique(['operator_id','year','month']);
                $table->double('final_cost_campaign')->default(0.0000);
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
        Schema::dropIfExists('final_cost_reports');
    }
}
