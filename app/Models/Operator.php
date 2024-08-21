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

class Operator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "operators";
    protected $primaryKey = 'id_operator';


    protected $fillable = [
        'id_operator',
        'country_id',
        'operator_name',
        'display_name',
        'country_name',
        'status',
        'service_type',
        'vat',
        'wht',
        'miscTax',
        'hostingCost',
        'content',
        'rnd',
        'bd',
        'miscCost',
    ];

    protected static function booted()
    {
        $userObj = Auth::user();

        if ($userObj == null)
            return 0;

        // dd($currentURL);
        // dd(Auth::user()->type);
        $user = Auth::user();

        $user_type = $user->type;
        $allowAllOperator = $user->WhowAccessAlOperator($user_type);






        $currentURLname = Route::current()->getName();

        //if($currentURLname=='report.summary' ||$currentURLname=='report.summary.daily.country' || $currentURLname=='report.user.filter.country' || $currentURLname=='report.user.filter.operator'){
        // dd($currentURLname);

        if (!$allowAllOperator) {

            static::addGlobalScope(new AllowOperaterScope);
        }
    }


    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function scopefilteroperator($query, $operator_ids)
    {
        return $query->whereIn('id_operator', $operator_ids);
    }

    public function revenueshare()
    {
        return $this->hasOne(Revenushare::class, 'operator_id', 'id_operator');
    }

    public function RevenushareByDate()
    {
        return $this->hasMany(RevenushareByDate::class, 'operator_id', 'id_operator');
    }
    public function VatByDate()
    {
        return $this->hasMany(VatByDate::class, 'operator_id', 'id_operator');
    }
    public function WhtByDate()
    {
        return $this->hasMany(WhtByDate::class, 'operator_id', 'id_operator');
    }
    public function MiscTax()
    {
        return $this->hasMany(MiscTax::class, 'operator_id', 'id_operator');
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'operator_id', 'id_operator');
    }

    public function service_history()
    {
        return $this->hasMany(Service::class, 'operator_id', 'id_operator');
    }

    public function summary_report()
    {
        return $this->hasMany(SummaryReportData::class, 'operator_id', 'id_operator');
    }

    public function company_operators()
    {
        return $this->hasOne(CompanyOperators::class, 'operator_id', 'id_operator');
    }

    public static function getOperator($id)
    {
        // $query = Operator::whereId($id)->get();
        $categories = Operator::query();
        // $categories= $query->OperatorsStatus()->getOperator($id)->get();
        // $categories = Operator::find($id);

        return $categories;
    }

    public function scopeGetOperatorByCountryId($query, $country_id)
    {
        return $query->where('country_id', $country_id);
    }

    public function scopeGetOperatorByBusinessType($query, $business_type)
    {
        return $query->where('business_type', $business_type);
    }

    public static function scopeStatus($query, $status)
    {
        return $query->where("status", $status);
    }

    public function scopeGetOperatorByOperatorId($query, $id_operator)
    {
        return $query->whereIn('id_operator', $id_operator);
    }

    public function scopefilterOperatorID($query, $id_operator)
    {
        return $query->where('id_operator', $id_operator);
    }

    public function scopeNotInOperators($query, $id_operators)
    {
        return $query->whereNotIn('id_operator', $id_operators);
    }

    public function scopegetOperatorIdByName($query, $operator_name)
    {
        return $query->where('operator_name', 'like', '%' . $operator_name . '%');
    }

    public function scopegetOperatorByName($query, $operator_name)
    {
        return $query->where('operator_name', '=', $operator_name);
    }

    public function revenue_reconcile()
    {
        return $this->hasMany(RevenueReconciles::class, 'operator_id', 'id_operator');
    }

    public function getOperatorName($operator)
    {
        return isset($operator->display_name) && $operator->display_name ? $operator->display_name : $operator->operator_name;
    }

    public function scopeGetCountryIds($query, $operator_id)
    {
        return $query->whereIn('id_operator', $operator_id);
    }
    public function scopeGetByCountryIds($query, $country_id)
    {
        return $query->whereIn('country_id', $country_id);
    }
    public function account_manager()
    {
        return $this->hasOne(UsersOperatorsServices::class, 'id_operator', 'id_operator')->orderBy('updated_at', 'desc');
    }
}
