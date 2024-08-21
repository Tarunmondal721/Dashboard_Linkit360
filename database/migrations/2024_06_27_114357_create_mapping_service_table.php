<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMappingServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasTable('mapping_service') ) {
            Schema::create('mapping_service', function (Blueprint $table) {
                $table->id();
                $table->foreignId("operator_id")->nullable();
                $table->string("service_id")->nullable();
                $table->string("operator_name")->nullable();
                $table->string("service_name")->nullable();
                $table->string("keyword")->nullable();
                $table->string("mapping_service")->nullable();
                $table->string("cycle")->nullable();
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
        Schema::dropIfExists('mapping_service');
    }
}
