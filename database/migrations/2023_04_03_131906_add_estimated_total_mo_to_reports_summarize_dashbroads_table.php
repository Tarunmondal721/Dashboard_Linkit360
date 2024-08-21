<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstimatedTotalMoToReportsSummarizeDashbroadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports_summarize_dashbroads', function (Blueprint $table) {
            $table->string('estimated_total_mo')->after('estimated_mo');
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
            $table->dropColumn('estimated_total_mo');
        });
    }
}
