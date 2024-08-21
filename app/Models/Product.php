<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'doman',
        'analytical_id',
        'status',
    ];
    public function scopeFindById($query,$id)
    {
        return $query->where('id', $id);
    }
    public function status($status){
        if((int)$status == 1){
            return "Success";
        }else{
            return "Failure";
        }
    }
}
