<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingService extends Model
{
    use HasFactory;
    protected $table = "mapping_service";
    protected $guarded = ["id"];
}
