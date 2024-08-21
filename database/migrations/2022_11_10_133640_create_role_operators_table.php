<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_operators', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer('operator_id');
            $table->bigInteger('operator_id')->unsigned()->nullable();
            $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
            // $table->integer('role_id');
            $table->bigInteger('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('SET NULL')->onUpdate('SET NULL');
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
        Schema::dropIfExists('role_operators');
    }
}
