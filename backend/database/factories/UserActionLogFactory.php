<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserActionLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActionLogFactory extends Factory
{
    protected $model = UserActionLog::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(['login', 'update', 'delete']),
            'description' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}