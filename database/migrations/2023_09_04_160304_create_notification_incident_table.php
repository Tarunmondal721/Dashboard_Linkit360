<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationIncidentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_incident', function (Blueprint $table) {
            $table->id();
            $table->integer('country_id');
            $table->integer('operator_id');
            $table->string('category');
            $table->string('subject');
            $table->string('details');
            $table->dateTime('time_incident');
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
        Schema::dropIfExists('notification_incident');
    }
}
