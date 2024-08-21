<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentGrossRevenueToReportsSummarizeDashbroads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports_summarize_dashbroads', function (Blueprint $table) {
            //
            $table->double('current_gross_revenue');
            $table->double('current_gross_revenue_usd');
            $table->double('estimated_gross_revenue');
            $table->double('estimated_gross_revenue_usd');
            $table->double('last_gross_revenue');
            $table->double('last_gross_revenue_usd');
            $table->double('prev_gross_revenue');
            $table->double('prev_gross_revenue_usd');
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
            //
        });
    }
}
