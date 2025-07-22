<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'task_status_id',
        'title',
        'description',
        'assigned_to',
        'created_by',
        'start_date',
        'due_date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, foreignKey: 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function logs()
    {
        return $this->hasMany(TaskStatusLog::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }
}
