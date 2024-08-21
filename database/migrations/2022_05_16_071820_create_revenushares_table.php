<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevenusharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revenushares', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('operator_id');
            $table->bigInteger('operator_id')->unsigned()->nullable();
            $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
            $table->decimal('operator_revenue_share',15,4);
            $table->decimal('merchant_revenue_share',15,4);
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
        Schema::dropIfExists('revenushares');
    }
}
