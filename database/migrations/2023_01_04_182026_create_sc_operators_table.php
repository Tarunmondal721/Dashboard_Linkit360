<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sc_operators')) {
            Schema::create('sc_operators', function (Blueprint $table) {
                $table->bigIncrements('id_operator');
                $table->unique(['country_id','operator_name']);
                $table->bigInteger('country_id');
                $table->string('operator_name');
                $table->string('country_name');
                $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('sc_operators');
    }
}
