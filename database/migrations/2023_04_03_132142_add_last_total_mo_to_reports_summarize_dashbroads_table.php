<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastTotalMoToReportsSummarizeDashbroadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports_summarize_dashbroads', function (Blueprint $table) {
            $table->string('last_total_mo')->after('last_mo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports_summarize_dashbroads', function (Blueprint $table) {
            $table->dropColumn('last_total_mo');
        });
    }
}
