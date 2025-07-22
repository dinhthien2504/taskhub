<?php

namespace App\Http\Resources\Projects;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'owner_id' => $this->owner_id,
            'owner_name' => $this->owner->name,
            'status' => $this->status,
            'tasks_count' => $this->tasks_count,
            'avatar' => $this->owner->avatar
        ];
    }
}
