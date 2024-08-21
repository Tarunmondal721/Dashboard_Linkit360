<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ServiceHistory;
use App\Scopes\AllowServiceScope;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_service',
        'service_name',
        'operator_id',
        'operator_name',
        'dascription',
        'service_type',
        'revenue_share',
        'sdc',
        'price',
        'keyword',
        'owner',
        'keyword_complete',

    ];

    protected $primaryKey = 'id_service';

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
            static::addGlobalScope(new AllowServiceScope);
        }
    }

    public function operator(){
        return $this->hasOne(Operator::class,'id_operator', 'operator_id');
    }

    public function service_history(){
        return $this->hasMany(ServiceHistory::class, 'id_service', 'id_service');
    }

    public function scopeGetserviceByOperatorId($query,$operator_id)
    {
        return $query->where('operator_id', $operator_id);
    }

    public function scopeGetserviceById($query,$id_service)
    {
        return $query->where('id_service', $id_service);
    }
     public function scopeGetserviceByIds($query,$id_service)
    {
        return $query->whereIn('id_service', $id_service);
    }

    public function scopeGetserviceBykeyword($query,$keyword)
    {
        return $query->where('keyword',$keyword);
    }

}
