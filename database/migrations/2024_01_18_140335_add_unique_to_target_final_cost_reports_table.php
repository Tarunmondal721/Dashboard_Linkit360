<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToTargetFinalCostReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('final_cost_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('final_cost_reports', 'id_service')) {
                $table->bigInteger('id_service')->unsigned()->nullable()->after('operator_id');
                $table->unique(['id_service','year','month']);
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
        Schema::table('final_cost_reports', function (Blueprint $table) {
            $table->dropColumn([
                'id_service'
            ]);
        });
    }
}
