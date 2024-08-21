<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetOpexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::create('target_opex', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });*/

        if (!Schema::hasTable('target_opex')) {
            Schema::create('target_opex', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('SET NULL')->onUpdate('SET NULL');
                $table->integer('year');
                $table->integer('month');
                $table->string('key');
                $table->unique(['company_id','year','month']);
                $table->double('opex')->default(0.0000);
                $table->double('target_opex')->default(0.0000);
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
        Schema::dropIfExists('target_opex');
    }
}
