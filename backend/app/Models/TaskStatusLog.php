<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatusLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'task_id',
        'old_status_id',
        'new_status_id',
        'user_id',
        'start_time',
        'end_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function oldStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'new_status_id');
    }

}
