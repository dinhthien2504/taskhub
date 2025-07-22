<?php

namespace App\Http\Resources\CheckIns;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckInResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'check_in_time' => $this->check_in_time ?? null,
            'check_out_time' => $this->check_out_time ?? null,
            'date' => $this->date ?? null,
            'status' => $this->status ?? null,
            'user' => $this->user ?? null,
            'is_late' => $this->is_late ?? false,
            'late_by_minutes' => $this->late_by_minutes ?? null
        ];
    }
}