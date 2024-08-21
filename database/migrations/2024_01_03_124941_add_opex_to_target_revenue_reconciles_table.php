<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOpexToTargetRevenueReconcilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target_revenue_reconciles', function (Blueprint $table) {
            if (!Schema::hasColumn('target_revenue_reconciles', 'opex')) {
                $table->double('opex')->default(0.0000)->after('pnl');
                $table->double('ebida')->default(0.0000)->after('opex');
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
        Schema::table('target_revenue_reconciles', function (Blueprint $table) {
            $table->dropColumn([
                'opex',
                'ebida'
            ]);
        });
    }
}
