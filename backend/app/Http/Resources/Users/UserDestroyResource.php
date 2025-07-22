<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDestroyResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'deleted_ids' => $this['deleted_ids'],
            'not_deleted_names' => $this['not_deleted_names']
        ];
    }
}
