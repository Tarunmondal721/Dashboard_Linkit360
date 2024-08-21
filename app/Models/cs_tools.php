<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cs_tools extends Model
{
    use HasFactory;

    protected $fillable = [
        'msisdn',
        'operator_id',
        'operator_name',
        'source',
        'country_name',
        'country_id',
        'service',
        'type',
        'status',
        'cycle',
        'adnet',
        'freemium_end_date',
        'revenue',
        'subs_date',
        'renewal_date',
        'schedule_charge',
        'last_charge_attempt',
        'unsubs_from',
        'subs_from',
        'service_price',
        'profile_status',
        'publisher',
        'handset',
        'browser',
        'trxid',
        'pixel',
        'telco_api_url',
        'telco_api_response',
        'telco_api_hit_date',
        'sms_send_date',
        'sms_content',
        'status_sms',
    ];
    public static function scopegetByMsisd($query,$msisdn)
    {
        return $query->where("msisdn",$msisdn);
    }

}
