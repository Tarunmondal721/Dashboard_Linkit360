<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrevTotalMoToReportsSummarizeDashbroadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports_summarize_dashbroads', function (Blueprint $table) {
            $table->string('prev_total_mo')->after('prev_mo');
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
            $table->dropColumn('prev_total_mo');
        });
    }
}
