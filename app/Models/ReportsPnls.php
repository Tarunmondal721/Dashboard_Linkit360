<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportsPnls extends Model
{
    use HasFactory;
    protected $table = "reports_pnls";


    public function scopeGetRecordByDate($query,$date)
    {
        return $query->where('date', $date);
    }

    public function scopefilterDateRange($query,$from,$to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopeGetRecordByOperator($query,$operator)
    {
        return $query->where('operator', $operator);
    }

    public function scopeGetRecordByService($query,$service)
    {
        return $query->where('service', $service);
    }

    public function scopeGetRecordByPublisher($query,$publisher)
    {
        return $query->where('publisher', $publisher);
    }

    public function scopeGetRecordByCountry($query,$country)
    {
        return $query->where('country', $country);
    }

    public function scopeGetRecordByUrlCampaign($query,$url_campaign)
    {
        return $query->where('url_campaign', $url_campaign);
    }

    public function scopeGetRecordByUrlService($query,$url_service)
    {
        return $query->where('url_service', $url_service);
    }
}
