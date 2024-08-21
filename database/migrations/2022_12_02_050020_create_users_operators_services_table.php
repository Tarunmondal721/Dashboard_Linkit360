<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersOperatorsServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasTable('users_operators_services') ) {
            Schema::create('users_operators_services', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                // $table->integer('id_operator');
                $table->bigInteger('id_operator')->unsigned()->nullable();
                $table->foreign('id_operator')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
                // $table->integer('id_service');
                $table->bigInteger('id_service')->unsigned()->nullable();
                $table->foreign('id_service')->references('id_service')->on('services')->onDelete('SET NULL')->onUpdate('SET NULL');
                $table->unique(['user_id','id_operator','id_service']);
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
        Schema::dropIfExists('users_operators_services');
    }
}
