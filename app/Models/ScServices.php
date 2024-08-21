<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScServices extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'company_id',
        'operator_id',
        'operator_name',
        'exit_operator',
        'service_name',
        'aggregator_status',
        'aggregator',
        'subkeyword',
        'short_code',
        'type',
        'is_freemium',
        'freemium',
        'revenue_share',
        'service_price',
        'pmo',
        'channel',
        'cycle',
        'account_manager',
        'backend',
        'subscription_keyword',
        'unsubscription_keyword',
        'portal_information',
        'subs_sms',
        'unsubs_sms',
        'renewal_sms',
        'campaign_type',
        'campaign_url',
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
        'url_cs_tools_main',
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
        'status_intregration',
        'project_start_date',
        'project_end_date',
        'go_live_date',
        'schedule_payment',
        'payment_come_date',
        'is_active',
        'infra_team',
        'is_draf',
        'status_count',
        'is_golive',
        'note',
        'percentage'
    ];

    // protected $casts = [
    //     'currency_service_price' => 'json',
    //     'matrix_enternal_team' => 'json',
    //     'matrix_client' => 'json',
    //     'matrix_telco' => 'json',
    // ];
    public function country(){
        return $this->hasOne(Country::class,'id', 'country_id');
    }
    public function operator()
    {
        return $this->hasOne(Operator::class, 'id_operator', 'operator_id');
    }
    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }
    public function pmouser()
    {
        return $this->hasOne(User::class, 'id', 'pmo');
    }
    public function accountManager()
    {
        return $this->hasOne(User::class, 'id', 'account_manager');
    }
    public function backenduser()
    {
        return $this->hasOne(User::class, 'id', 'backend');
    }
    public function infra()
    {
        return $this->hasOne(User::class, 'id', 'infra_team');
    }
    public function csteam()
    {
        return $this->hasOne(User::class, 'id', 'cs_team');
    }
    public function scopefindServices($query,$id)
    {
        return $query->where('id', $id);
    }
    public function scopefindByCountry($query,$country_id)
    {
        return $query->where('country_id', $country_id);
    }
    public function scopefindByOperator($query,$operator_id)
    {
        return $query->where('operator_id', $operator_id);
    }
    public function scopefindByAccountManager($query,$account_manager)
    {
        return $query->where('account_manager', $account_manager);
    }
    public function scopefindByPmo($query,$pmo)
    {
        return $query->where('pmo', $pmo);
    }
    public function scopefindByBackend($query,$backend)
    {
        return $query->where('backend', $backend);
    }

    public function scopefindByStatus($query,$status)
    {
        return $query->where('is_active', $status);
    }
    public function scopefindByIntergration($query,$inter)
    {
        return $query->where('status_intregration', $inter);
    }

    public function scopefilterDateRange($query,$from,$to)
    {
        return $query->whereBetween('project_start_date', [$from, $to]);

    }
    public function scopefindByOperatorname($query,$operator_name)
    {
        return $query->where('operator_name', $operator_name);
    }
}
