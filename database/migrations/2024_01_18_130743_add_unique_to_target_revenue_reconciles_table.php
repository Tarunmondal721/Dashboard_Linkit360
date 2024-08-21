<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueToTargetRevenueReconcilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('target_revenue_reconciles', function (Blueprint $table) {
            if (Schema::hasColumn('target_revenue_reconciles', 'dkey')) {
                $table->renameColumn('dkey', 'id_service')->unsigned()->nullable();
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
        Schema::table('target_revenue_reconciles', function (Blueprint $table) {
            $table->dropColumn([
                'id_service'
            ]);
        });
    }
}
