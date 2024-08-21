<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CompanyOperators;
use App\Scopes\AllowCompanyScope;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
    ];

    protected static function booted()
    {
        $userObj = Auth::user();
        if($userObj == null)
        return 0;

        $user = Auth::user();
        $user_type = $user->type;
        $allowAllOperator = $user->WhowAccessAlOperator($user_type);
        $currentURLname = Route::current()->getName();
        if(!$allowAllOperator){
            static::addGlobalScope(new AllowCompanyScope);
        }
    }

    public function rules(){
        return [
            'name'=>'required|unique:companies',
        ];
    }

    public function company_operators(){
        return $this->hasMany(CompanyOperators::class, 'company_id', 'id');
    }

    public function scopeGetCompanyByCompanyId($query,$company_ids)
    {
        return $query->whereIn('id', $company_ids);
    }

    public function scopeGetById($query,$id){
        return $query->where('id',$id);
    }
}
