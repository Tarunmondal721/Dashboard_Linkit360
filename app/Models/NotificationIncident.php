<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationIncident extends Model
{
    use HasFactory;
    protected $table = "notification_incident";
    protected $guarded = ['id'];
}
