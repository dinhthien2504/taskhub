<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckInLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'user_id',
        'date',
        'check_in_time',
        'check_out_time',
        'status',
        'is_late',
        'late_by_minutes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
