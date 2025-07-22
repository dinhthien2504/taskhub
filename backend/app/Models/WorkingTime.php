<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingTime extends Model
{
    protected $fillable = [
        'start_time', 
        'late_after', 
        'end_time'
    ];
}
