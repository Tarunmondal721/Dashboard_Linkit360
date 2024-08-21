<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteScServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sc_services', function (Blueprint $table) {
            if(!Schema::hasColumn('sc_services','is_golive')){
                $table->string('is_golive')->default('no')->after('go_live_date');
                $table->longText('note')->nullable()->after('is_golive');
                $table->double('percentage',10,2)->default(0.0)->after('note');
                $table->string('url_cs_tools_main')->nullable()->after('url_cs_tools');
                $table->integer('exit_operator')->default(1)->after('operator_name');

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
        Schema::table('sc_services',function(Blueprint $table){
            $table->dropColumn(['is_golive','note','percentage','url_cs_tools_main','exit_operator']);
        });
    }
}
