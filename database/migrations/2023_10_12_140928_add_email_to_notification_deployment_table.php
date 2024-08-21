<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToNotificationDeploymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_deployment', function (Blueprint $table) {
            //
            if (!Schema::hasColumn('downtime', 'email')) {

                $table->string('downtime')->nullable();
                $table->string('email')->nullable();
            }

            if (Schema::hasColumn('downtime_start', 'downtime_end')) {
                $table->dropColumn('downtime_start');
                $table->dropColumn('downtime_end');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notification_deployment', function (Blueprint $table) {
            //

        });
    }
}
