<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsPnlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasTable('reports_pnls') ) {
            Schema::create('reports_pnls', function (Blueprint $table) {
                $table->id();
                $table->date('date');
                $table->string('publisher',100);
                $table->string('operator',100);
                $table->string('service');
                $table->integer('and');
                $table->integer('mo_received');
                $table->integer('mo_postback');
                $table->bigInteger('landing');
                $table->decimal('cr_mo_received',10,2);
                $table->decimal('cr_mo_postback',10,2);
                $table->longText('url_campaign');
                $table->longText('url_service');
                $table->string('client',100);
                $table->string('aggregator');
                $table->string('country',100);
                $table->decimal('sbaf',10,2);
                $table->decimal('saaf',10,2);
                $table->decimal('payout',10,4);
                $table->decimal('price_per_mo',10,4);
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
        Schema::dropIfExists('reports_pnls');
    }
}
