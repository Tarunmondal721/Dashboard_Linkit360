<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Casts\Attribute;

class UserPivot extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'report_type',
        'report_column',
        'description',
    ];
    public static function scopeGetByUserId($query,$user_id)
    {
        return $query->where("user_id",$user_id);
    }
}
