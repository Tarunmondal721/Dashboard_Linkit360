<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Revenushare;
use App\Models\RevenushareByDate;
use App\Models\Service;
use App\Models\ServiceHistory;
use App\Models\SummaryReportData;
use App\Models\CompanyOperators;
use App\Models\RevenueReconciles;
use App\Scopes\AllowOperaterScope;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class UserActivity extends Model
{
    use HasFactory;

    protected $table = "users_activity";

    protected $fillable = [
        'user_id',
        'action',
    ];

    public function user(){
        return $this->hasOne(User::class,'id', 'user_id');
    }
}