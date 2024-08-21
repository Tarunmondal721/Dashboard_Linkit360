<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceChecklists extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'pmo_1',
        'pmo_2',
        'pmo_3',
        'pmo_4',
        'pmo_5',
        'pmo_6',
        'pmo_7',
        'pmo_8',
        'pmo_9',
        'pmo_10',
        'pmo_11',
        'pmo_12',
        'pmo_13',
        'pmo_14',
        'pmo_15',
        'pmo_16',
        'pmo_17',
        'pmo_18',
        'pmo_19',
        'infra_1',
        'infra_2',
        'infra_3',
        'infra_4',
        'infra_5',
        'infra_6',
        'infra_7',
        'business_1',
        'business_2',
        'business_3',
        'business_4',
        'business_5',
        'business_6',
        'cs_1',
        'cs_2',
        'cs_3',
        'cs_4',
        'cs_5',
        'finance_1',
        'finance_2',
        'finance_3',
        'finance_4',
    ];
    public function scopeGetbyserviceid($query, $id){
        return $query->where ('service_id',$id);
    }
}
