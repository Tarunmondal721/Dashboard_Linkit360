<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'signature',
        'command',
        'date',
        'cron_start_date',
        'cron_end_date',
        'total_in_up',
        'table_name',
        'status',
    ];
}
