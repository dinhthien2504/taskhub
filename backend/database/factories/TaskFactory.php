<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = \App\Models\Task::class;

    public function definition()
    {
        return [
            'project_id' => Project::factory(),
            'task_status_id' => TaskStatus::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->optional()->paragraph,
            'assigned_to' => User::factory(),
            'created_by' => User::factory(),
            'start_date' => $this->faker->date(),
            'due_date' => $this->faker->date(),
        ];
    }
}