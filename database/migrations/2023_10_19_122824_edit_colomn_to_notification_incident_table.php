<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColomnToNotificationIncidentTable extends Migration
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
            $table->string('status')->nullable();

            $table->integer('operator_id')->nullable()->change();
            $table->longText('details')->nullable()->change();
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
