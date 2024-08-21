<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryReportData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'operator_id',
        'fmt_success',
        'fmt_failed',
        'mt_success',
        'mt_failed',
        'gross_revenue',
        'reg',
        'unreg',
        'total',
        'purge',
        'date',
        'created_at',
        'updated_at',
    ];
}
