<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Operator;

class role_operators extends Model
{
    use HasFactory;

    protected $table = "role_operators";

    protected $fillable = [
        'operator_id',
        'role_id',
    ];

    public function Roles(){
        return $this->hasOne(Roles::class, 'id', 'role_id');
    }

    public function Operator(){
        return $this->hasMany(Operator::class, 'id_operator', 'operator_id');
    }

    public function scopeGetRoleOperator($query,$role_id)
    {
        return $query->where('role_id', $role_id);

    }
}
