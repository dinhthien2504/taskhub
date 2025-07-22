<?php

namespace App\Http\Resources\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskShowResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start_date' => $this->start_date ?? null,
            'due_date' => $this->due_date,
            'task_status_id' => $this->task_status_id,
            'assigned_to' => $this->assigned_to,
            'assigned_name' => $this->assignee->name ?? null,
            'name_project' => $this->project->name ?? null,
            'logs' => $this->logs,
            'comments' => $this->comments,
            'description' => $this->description,
        ];
    }
}
