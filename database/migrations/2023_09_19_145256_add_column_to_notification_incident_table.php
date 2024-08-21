<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToNotificationIncidentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_incident', function (Blueprint $table) {
            //
            $table->string("number_ticket")->nullable();
            $table->string('created_by')->nullable();
            $table->string('classification')->nullable();
            $table->string('severty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_incident', function (Blueprint $table) {
            //
        });
    }
}
