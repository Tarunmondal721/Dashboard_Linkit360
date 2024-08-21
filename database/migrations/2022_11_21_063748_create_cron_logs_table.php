<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCronLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasTable('cron_logs') ) {
            Schema::create('cron_logs', function (Blueprint $table) {
                $table->id();
                $table->longText('description');
                $table->string('signature');
                $table->string('command');
                $table->string('date')->nullable();
                $table->unique(['signature','date']);
                $table->dateTime('cron_start_date');
                $table->dateTime('cron_end_date');
                $table->integer('total_in_up')->nullable();
                $table->string('table_name');
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
        Schema::dropIfExists('cron_logs');
    }
}
