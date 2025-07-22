<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            \App\Models\User::class,
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        );
    }

    protected $attributes = [
        'guard_name' => 'web',
    ];
}
