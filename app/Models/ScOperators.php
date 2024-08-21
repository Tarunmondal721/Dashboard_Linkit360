<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScOperators extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_operator',
        'country_id',
        'operator_name',
        'display_name',
        'country_name',
        'status',
    ];
    public function scopefindById($query,$id_operator)
    {
        return $query->where('id_operator', $id_operator);
    }
    public function scopefindByCountryId($query,$country_id)
    {
        return $query->where('country_id', $country_id);
    }
    public function scopefindByOperatorName($query,$operator_name)
    {
        return $query->where('operator_name', $operator_name);
    }

}
