<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVatByDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vat_by_dates', function (Blueprint $table) {
               $table->id();
                $table->bigInteger('operator_id')->unsigned();
                $table->decimal('vat',15,4)->default(0.0000);
                $table->year('year')->nullable();
                $table->string('month')->nullable();
                $table->string('key')->nullable();
                $table->unique(['operator_id','key']);
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
        Schema::dropIfExists('vat_by_dates');
    }
}
