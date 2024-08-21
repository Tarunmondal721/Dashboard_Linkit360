<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToScServiceProgresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sc_service_progres', function (Blueprint $table) {
            if (!Schema::hasColumn('sc_service_progres', 'note')) {
                $table->string('note')->nullable()->after('status');
                $table->string('file')->nullable()->after('note');
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
        Schema::table('sc_service_progres', function (Blueprint $table) {
            $table->dropColumn([
                'note',
                'file'
            ]);
        });
    }
}
