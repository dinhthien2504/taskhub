<?php

namespace App\Http\Resources\Campaigns;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
            'created_at' => $this->created_at,
            'creator_name' => $this->creator?->name,
            'deleted_at' => $this->deleted_at,
            'template_id' => $this->template_id
        ];
    }
}