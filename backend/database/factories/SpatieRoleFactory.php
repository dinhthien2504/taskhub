<?php

namespace Database\Factories;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpatieRoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->jobTitle,
            'guard_name' => 'web', // Quan trọng khi dùng Spatie
        ];
    }
}
