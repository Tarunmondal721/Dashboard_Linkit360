<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScServiceProgresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sc_service_progres')) {
            Schema::create('sc_service_progres', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('id_service');
                $table->bigInteger('id_service_status');
                $table->unique(['id_service','id_service_status']);
                $table->date('dute_date')->nullable();
                $table->date('complete_due_date')->nullable();
                $table->string('status');
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
        Schema::dropIfExists('sc_service_progres');
    }
}
