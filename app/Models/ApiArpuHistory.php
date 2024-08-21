<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiArpuHistory extends Model
{
    use HasFactory;
    protected $table = "api_arpu_histories";
    protected $guarded = ["id"];
}
