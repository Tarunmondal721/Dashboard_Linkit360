<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevenueReconcilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('revenue_reconciles', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
        if (!Schema::hasTable('revenue_reconciles')) {
            Schema::create('revenue_reconciles', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('country_id')->unsigned()->nullable();
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL')->onUpdate('SET NULL');

                $table->bigInteger('operator_id')->unsigned()->nullable();

                $table->integer('year');
                $table->integer('month');
                $table->string('key');
                $table->unique(['operator_id','year','month']);
                $table->string('currency');
                $table->double('revenue')->default(0.0000);
                $table->double('revenue_telco')->default(0.0000);
                $table->string('file')->nullable();
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
        Schema::dropIfExists('revenue_reconciles');
    }
}
