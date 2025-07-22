<?php

namespace App\Http\Resources\Tasks;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'task_status_id' => $this->task_status_id,
            'assigned_to' => $this->assignee->name ?? null,
            'avatar' => $this->assignee->avatar ?? null,
        ];
    }
}
