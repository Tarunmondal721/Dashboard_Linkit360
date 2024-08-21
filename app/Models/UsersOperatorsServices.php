<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersOperatorsServices extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','id_operator','id_service',];

    public function scopeGetOperaterServiceByUserId($query,$user_id)
    {
        return $query->where('user_id', $user_id);
    }
    public function scopeGetOperaterServiceByUserIdIn($query,$user_id)
    {
        return $query->whereIn('user_id', $user_id);
    }
    public function scopeGetOperaterServiceByOperatorIds($query,$id_operator)
    {
        return $query->whereIn('id_operator', $id_operator);
    }
    public function scopeGetOperaterServiceByNotInServiceId($query,$id_service)
    {
        return $query->whereNotIn('id_service', $id_service);
    }
    public function operator(){
        return $this->hasOne(Operator::class, 'id_operator', 'id_operator');
    }
    public function service(){
        return $this->hasOne(Service::class, 'id_service', 'id_service');
    }
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
