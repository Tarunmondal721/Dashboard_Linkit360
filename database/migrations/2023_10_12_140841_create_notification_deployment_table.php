<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationDeploymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('notification_deployment')) {

            Schema::create('notification_deployment', function (Blueprint $table) {
                $table->id();
                $table->integer('country_id');
                $table->integer('operator_id');
                $table->string('category');
                $table->string('subject');
                $table->string('message');
                $table->string('activity_name');
                $table->string('objective');
                $table->string('maintenance_detail');
                $table->dateTime('maintenance_start');
                $table->dateTime('maintenance_end');
                $table->dateTime('downtime_start');
                $table->dateTime('downtime_end');
                $table->string("service_impact");
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
        Schema::dropIfExists('notification_deployment');
    }
}
