<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationDeployment extends Model
{
    use HasFactory;
    protected $table = "notification_deployment";
    protected $guarded = ['id'];
}
