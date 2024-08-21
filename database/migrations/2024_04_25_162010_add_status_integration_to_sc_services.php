<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusIntegrationToScServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sc_services', function (Blueprint $table) {
            if(!Schema::hasColumn('sc_services','status_intregration')){
                $table->string('status_intregration')->nullable()->after('short_code');
                $table->date('project_start_date')->nullable()->after('status_intregration');
                $table->date('project_end_date')->nullable()->after('project_start_date');
                $table->date('go_live_date')->nullable()->after('project_end_date');
                $table->date('schedule_payment')->nullable()->after('go_live_date');
                $table->date('payment_come_date')->nullable()->after('schedule_payment');
                $table->string('operator_name')->nullable()->after('operator_id');
                $table->string('is_freemium')->default('yes')->after('cycle');
                $table->integer('is_active')->nullable()->after('is_draf');

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
        Schema::table('sc_services', function (Blueprint $table) {
            $table->dropColumn([
                'status_intregration',
                'project_start_date',
                'project_end_date',
                'go_live_date',
                'schedule_payment',
                'payment_come_date',
                'is_active'
            ]);
        });
    }
}
