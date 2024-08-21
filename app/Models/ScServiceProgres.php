<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScServiceProgres extends Model
{
    use HasFactory;
    protected $fillable = [
    'id_service',
    'id_service_status',
    'dute_date',
    'complete_due_date',
    'status',
    'note',
    'file'
    ];

    public function scopeFindByIdService($query,$id_service)
    {
        return $query->where('id_service', $id_service);
    }
    public function serviceStatus(){
        return $this->hasOne(ScServiceStatus::class,'id', 'id_service_status');
    }
    public function scopeFindByIdStatus($query,$id_status)
    {
        return $query->where('id_service_status', $id_status);
    }
}
