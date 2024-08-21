<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevenushareByDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('revenushare_by_dates')) {
            Schema::create('revenushare_by_dates', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('operator_id')->unsigned();
                $table->decimal('operator_revenue_share',15,4);
                $table->decimal('merchant_revenue_share',15,4);
                $table->year('year');
                $table->string('month');
                $table->string('key');
                $table->unique(['operator_id','key']);
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
        Schema::dropIfExists('revenushare_by_dates');
    }
}
