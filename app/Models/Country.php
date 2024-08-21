<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operator;
use App\Scopes\AllowCountryScope;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'country',
        'country_code',
        'currency_code',
        'usd',
        'flag',
    ];
    protected $table = 'countries';

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
            static::addGlobalScope(new AllowCountryScope);
        }
    }

    public function operators(){
        return $this->hasMany(Operator::class)->Status(1);
    }
    public function scopeGetCountryByCountryId($query,$id)
    {
        return $query->whereIn('id', $id);

    }
    public function scopeGetCountryByCurrencyCode($query,$currency_code)
    {
        return $query->where('currency_code','like',  '%' .  $currency_code .'%');

    }

    public function scopeGetByNane($query,$name){
        return $query->where('country',$name);
    }

    public function scopeGetByCountrycode($query,$name){
        return $query->where('country_code',$name);
    }

    public function scopeGetById($query,$id){
        return $query->where('id',$id);
    }

}
