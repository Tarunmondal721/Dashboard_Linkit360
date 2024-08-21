<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryArpu extends Model
{
    use HasFactory;
    protected $table = "countries_arpu";
    protected $guarded = ["id"];
}
