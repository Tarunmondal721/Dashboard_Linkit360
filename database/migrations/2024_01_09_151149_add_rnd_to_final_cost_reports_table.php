<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRndToFinalCostReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('final_cost_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('final_cost_reports', 'rnd')) {
                $table->double('rnd')->default(0.0000)->after('final_cost_campaign');
                $table->double('content')->default(0.0000)->after('rnd');
                $table->double('fun_basket')->default(0.0000)->after('content');
                $table->double('bd')->default(0.0000)->after('fun_basket');
                $table->double('platform')->default(0.0000)->after('bd');
                $table->double('hosting')->default(0.0000)->after('platform');
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
                'rnd',
                'content',
                'fun_basket',
                'bd',
                'platform',
                'hosting'
            ]);
        });
    }
}
