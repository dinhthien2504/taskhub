<?php

namespace App\Http\Resources\EmailTemplates;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailTemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subject' => $this->subject,
            'content' => $this->content,
            'description' => $this->description,
            'type' => $this->type,
            'is_active' => $this->is_active,
            'created_by' => $this->creator->name ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
