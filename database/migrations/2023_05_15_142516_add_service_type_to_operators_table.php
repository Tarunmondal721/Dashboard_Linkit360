<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceTypeToOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operators', function (Blueprint $table) {
            if (!Schema::hasColumn('operators', 'service_type')) {
                $table->string('service_type')->nullable()->after('status');
                $table->integer('vat')->nullable()->after('service_type');
                $table->integer('wht')->nullable()->after('vat');
                $table->integer('miscTax')->nullable()->after('wht');
                $table->integer('hostingCost')->nullable()->after('miscTax');
                $table->integer('content')->nullable()->after('hostingCost');
                $table->integer('rnd')->nullable()->after('content');
                $table->integer('bd')->nullable()->after('rnd');
                $table->integer('miscCost')->nullable()->after('bd');
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
        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn([
                'service_type',
                'vat',
                'wht',
                'miscTax',
                'hostingCost',
                'content',
                'rnd',
                'bd',
                'miscCost'
            ]);
        });
    }
}
