<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return voidw
     */
    public function up()
    {
        if (!Schema::hasTable('sc_services')) {
            Schema::create('sc_services', function (Blueprint $table) {
                $table->id();
                $table->string('country_id')->nullable();
                $table->string('company_id')->nullable();
                $table->string('operator_id')->nullable();
                $table->string('service_name')->nullable();
                $table->string('aggregator_status')->default('yes')->nullable();
                $table->string('aggregator')->nullable();
                $table->string('subkeyword')->nullable();
                $table->string('short_code')->nullable();
                $table->string('type')->default('SUBSCRIPTION ')->nullable();
                $table->string('channel')->nullable();
                $table->string('cycle')->nullable();
                $table->string('freemium')->nullable();
                $table->longText('service_price')->nullable();
                $table->double('revenue_share',10,4)->default(0.0000);
                $table->string('account_manager');
                $table->string('pmo')->nullable();
                $table->string('backend')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sc_services');
    }
}
