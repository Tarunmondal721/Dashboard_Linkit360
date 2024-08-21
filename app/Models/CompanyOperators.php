<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operator;
use App\Models\Company;

class CompanyOperators extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "company_operators";

    protected $fillable = [
        'company_id',
        'operator_id',
    ];

    public function Company(){
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function Operator(){
        return $this->hasMany(Operator::class, 'id_operator', 'operator_id');
    }
    public function scopeGetOperator($query,$company_id)
    {
        return $query->where('company_id', $company_id);

    }
    // public function post()
    // {
    //     return $this->belongsTo(Operator::class, 'id_operator','operator_id');
    // }

    public function scopefilterOperatorID($query,$id_operator)
    {
        return $query->where('operator_id', $id_operator);
    }

    public function scopeGetCompanyIds($query,$operator_id)
    {
        return $query->whereIn('operator_id', $operator_id);
    }
}
