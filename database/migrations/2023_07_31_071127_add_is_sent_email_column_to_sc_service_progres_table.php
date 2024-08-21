<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSentEmailColumnToScServiceProgresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sc_service_progres', function (Blueprint $table) {
            //
            $table->boolean('is_sent_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sc_service_progres', function (Blueprint $table) {
            //
            Schema::dropColumn('is_sent_email');
        });
    }
}
