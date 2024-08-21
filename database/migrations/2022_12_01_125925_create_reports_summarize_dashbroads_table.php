<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsSummarizeDashbroadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasTable('reports_summarize_dashbroads') ) {
            Schema::create('reports_summarize_dashbroads', function (Blueprint $table) {
                $table->id();
                // $table->integer('company_id')->nullable();
                $table->bigInteger('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('SET NULL')->onUpdate('SET NULL');
                // $table->integer('country_id');
                $table->bigInteger('country_id')->unsigned()->nullable();
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('SET NULL')->onUpdate('SET NULL');
                // $table->integer('operator_id');
                $table->bigInteger('operator_id')->unsigned()->nullable();
                $table->foreign('operator_id')->references('id_operator')->on('operators')->onDelete('SET NULL')->onUpdate('SET NULL');
                $table->unique(['operator_id']);
                $table->double('current_revenue');
                $table->double('current_revenue_usd');
                $table->string('current_mo');
                $table->string('current_cost');
                $table->string('current_pnl');
                $table->float('current_price_mo', 15, 5)->default(0.00000);
                $table->float('current_usd_rev_share', 15, 5)->default(0.00000);
                $table->float('current_reg_sub', 15, 5)->default(0.00000);
                $table->float('current_30_arpu', 15, 5)->default(0.00000);
                $table->float('current_roi', 15, 5)->default(0.00000);
                $table->float('estimated_revenue');
                $table->float('estimated_revenue_usd');
                $table->string('estimated_mo');
                $table->string('estimated_cost');
                $table->string('estimated_pnl');
                $table->float('estimated_price_mo', 15, 5)->default(0.00000);
                $table->float('estimated_30_arpu', 15, 5)->default(0.00000);
                $table->float('estimated_roi', 15, 5)->default(0.00000);
                $table->double('last_revenue');
                $table->double('last_revenue_usd');
                $table->string('last_mo');
                $table->string('last_cost');
                $table->string('last_pnl');
                $table->float('last_price_mo', 15, 5)->default(0.00000);
                $table->float('last_usd_rev_share', 15, 5)->default(0.00000);
                $table->float('last_reg_sub', 15, 5)->default(0.00000);
                $table->float('last_30_arpu', 15, 5)->default(0.00000);
                $table->float('last_roi', 15, 5)->default(0.00000);
                $table->double('prev_revenue');
                $table->double('prev_revenue_usd');
                $table->string('prev_mo');
                $table->string('prev_cost');
                $table->string('prev_pnl');
                $table->float('prev_price_mo', 15, 5)->default(0.00000);
                $table->float('previous_usd_rev_share', 15, 5)->default(0.00000);
                $table->float('previous_reg_sub', 15, 5)->default(0.00000);
                $table->float('prev_30_arpu', 15, 5)->default(0.00000);
                $table->float('prev_roi', 15, 5)->default(0.00000);
                $table->date('date');

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
        Schema::dropIfExists('reports_summarize_dashbroads');
    }
}
