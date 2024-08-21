<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdServiceToRevenueReconcilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('revenue_reconciles', function (Blueprint $table) {
            if (!Schema::hasColumn('revenue_reconciles', 'id_service')) {
                $table->bigInteger('id_service')->unsigned()->nullable()->after('operator_id');
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
                'id_service'
            ]);
        });
    }
}
