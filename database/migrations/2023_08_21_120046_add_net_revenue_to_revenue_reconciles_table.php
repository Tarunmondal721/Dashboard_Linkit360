<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNetRevenueToRevenueReconcilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('revenue_reconciles', function (Blueprint $table) {
            if (!Schema::hasColumn('revenue_reconciles', 'net_revenue')) {
                $table->double('net_revenue')->default(0.0000)->after('revenue_telco');
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
        Schema::table('revenue_reconciles', function (Blueprint $table) {
            $table->dropColumn([
                'net_revenue'
            ]);
        });
    }
}
