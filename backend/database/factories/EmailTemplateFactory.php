<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'subject' => $this->faker->sentence(5),
            'content' => $this->faker->paragraph(),
            'description' => $this->faker->sentence(8),
            'type' => 'default',
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
