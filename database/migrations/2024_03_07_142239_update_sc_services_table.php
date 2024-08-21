<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateScServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sc_services', function (Blueprint $table) {
            if (!Schema::hasColumn('sc_services', 'is_airpay')) {
                $table->string('is_airpay')->default('no')->after('type');
                $table->string('service_type')->nullable()->after('is_airpay');
                $table->double('merchant_share',10,4)->default(0.0000)->after('service_type');
                $table->json('currency_service_price')->nullable()->after('merchant_share');
                // $table->string('reward')->default('no')->after('currency_code');
                $table->string('report_source')->nullable()->after('currency_service_price');
                $table->string('report_partner')->nullable()->after('report_source');
                $table->string('sub_domain_portal')->nullable()->after('report_partner');
                $table->string('portal_url')->nullable()->after('sub_domain_portal');
                $table->string('cms_portal')->nullable()->after('portal_url');
                $table->string('username_cms_portal')->nullable()->after('cms_portal');
                $table->string('password_cms_portal')->nullable()->after('username_cms_portal');
                $table->string('url_cs_tools')->nullable()->after('password_cms_portal');
                // $table->string('campaign_type')->nullable()->after('url_cs_tools');
                $table->string('url_postback')->nullable()->after('url_cs_tools');
                $table->string('url_campaign')->nullable()->after('url_postback');
                $table->string('product_brief_file')->nullable()->after('url_campaign');
                $table->string('faq_file')->nullable()->after('product_brief_file');
                $table->string('contract_file')->nullable()->after('faq_file');
                $table->string('merchant_coi_file')->nullable()->after('contract_file');
                $table->string('addendums_file')->nullable()->after('merchant_coi_file');
                $table->string('content_authority_letter')->nullable()->after('addendums_file');
                $table->string('cor_dgt_file')->nullable()->after('content_authority_letter');
                $table->json('matrix_enternal_team')->nullable()->after('cor_dgt_file');
                $table->json('matrix_client')->nullable()->after('matrix_enternal_team');
                $table->json('matrix_telco')->nullable()->after('matrix_client');
                $table->string('cs_team')->nullable()->after('matrix_telco');
                $table->string('infra_team')->nullable()->after('cs_team');
                $table->integer('is_draf')->default(0)->after('infra_team');




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
                'is_airpay',
                'service_type',
                'merchant_share',
                'currency_service_price',
                'report_source',
                'report_partner',
                'sub_domain_portal',
                'portal_url',
                'cms_portal',
                'username_cms_portal',
                'password_cms_portal',
                'url_cs_tools',
                // 'campaign_type',
                'url_postback',
                'url_campaign',
                'product_brief_file',
                'faq_file',
                'contract_file',
                'merchant_coi_file',
                'content_authority_letter',
                'addendums_file',
                'cor_dgt_file',
                'matrix_enternal_team',
                'matrix_client',
                'matrix_telco',
                'cs_team',
                'infra_team',
                'is_draf'
            ]);
        });
    }
}
