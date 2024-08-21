<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePnlSummeryMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( !Schema::hasTable('pnl_summery_months') ) {
            Schema::create('pnl_summery_months', function (Blueprint $table) {
                $table->id();
                $table->date('date')->nullable();
                $table->tinyInteger('type')->length(4)->nullable();
                $table->bigInteger('id_operator')->unsigned()->nullable();
                $table->bigInteger('country_id')->unsigned()->nullable();
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->year('year');
                $table->string('month');
                $table->string('key');
                $table->string('operator')->nullable();
                $table->unique(['id_operator','user_id','year','month']);
                // $table->unique(['id_operator','date']);
                $table->string('country_code')->nullable();
                $table->integer('mo_received')->nullable();
                $table->integer('mo_postback')->nullable();
                $table->decimal('cr_mo_received',30,2)->nullable();
                $table->decimal('cr_mo_postback',30,2)->nullable();
                $table->decimal('saaf',20,2)->nullable();
                $table->decimal('sbaf',20,2)->nullable();
                $table->decimal('cost_campaign',20,2)->nullable();
                $table->integer('clicks')->nullable();
                $table->decimal('ratio_for_cpa',20,2)->nullable();
                $table->decimal('cpa_price',20,2)->nullable();
                $table->decimal('cr_mo_clicks',20,2)->nullable();
                $table->decimal('cr_mo_landing',20,2)->nullable();
                $table->integer('mo')->nullable();
                $table->bigInteger('landing')->nullable();
                $table->integer('reg')->nullable();
                $table->integer('unreg')->nullable();
                $table->decimal('price_mo',30,4)->nullable();
                $table->integer('active_subs')->nullable();
                $table->decimal('rev_usd',20,4)->nullable();
                $table->decimal('rev',30,4)->nullable();
                $table->decimal('share',30,4)->nullable();
                $table->decimal('lshare',30,4)->nullable();
                $table->decimal('br',30,2)->nullable();
                $table->integer('br_success')->nullable();
                $table->integer('br_failed')->nullable();
                $table->decimal('fp',20,2)->nullable();
                $table->decimal('rnd',20,2)->nullable();
                $table->integer('fp_success')->nullable();
                $table->integer('fp_failed')->nullable();
                $table->decimal('dp',20,2)->nullable();
                $table->integer('dp_success')->nullable();
                $table->integer('dp_failed')->nullable();
                $table->decimal('other_cost',30,2)->nullable();
                $table->decimal('other_tax',30,2)->nullable();
                $table->decimal('misc_tax',30,2)->nullable();
                $table->decimal('hosting_cost',20,2)->nullable();
                $table->decimal('content',30,2)->nullable();
                $table->decimal('bd',30,2)->nullable();
                $table->decimal('platform',30,2)->nullable();
                $table->decimal('excise_tax',30,2)->nullable();
                $table->decimal('vat',30,2)->nullable();
                $table->decimal('end_user_revenue_after_tax',30,2)->nullable();
                $table->decimal('wht',30,2)->nullable();
                $table->decimal('rev_after_makro_share',30,2)->nullable();
                $table->decimal('discremancy_project',30,2)->nullable();
                $table->decimal('arpu_7',30,2)->nullable();
                $table->decimal('arpu_30',30,2)->nullable();
                $table->decimal('net_revenue',30,2)->nullable();
                $table->decimal('tax_operator',30,2)->nullable();
                $table->decimal('bearer_cost',30,2)->nullable();
                $table->decimal('shortcode_fee',30,2)->nullable();
                $table->decimal('waki_messaging',30,2)->nullable();
                $table->decimal('net_revenue_after_tax',30,2)->nullable();
                $table->decimal('end_user_rev_local_include_tax',30,2)->nullable();
                $table->decimal('end_user_rev_usd_include_tax',30,2)->nullable();
                $table->decimal('gross_usd_rev_after_tax',30,2)->nullable();
                $table->decimal('spec_tax',30,2)->nullable();
                $table->decimal('net_after_tax',30,2)->nullable();
                $table->decimal('government_cost',30,2)->nullable();
                $table->decimal('dealer_commision',30,2)->nullable();
                $table->decimal('uso',30,2)->nullable();
                $table->decimal('verto',30,2)->nullable();
                $table->decimal('agre_paxxa',30,2)->nullable();
                $table->decimal('net_income_after_vat',30,2)->nullable();
                $table->decimal('gross_revenue_share_linkit',30,2)->nullable();
                $table->decimal('gross_revenue_share_paxxa',30,2)->nullable();
                $table->decimal('pnl',30,2)->nullable();
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
        Schema::dropIfExists('pnl_summery_months');
    }
}
