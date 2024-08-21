<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingOperator extends Model
{
    use HasFactory;
    protected $table = "mapping_operators";
    protected $guarded = ["id"];
}
