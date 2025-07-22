<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(8),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'status' => 'pending',
            'created_by' => User::factory(),
            'template_id' => EmailTemplate::factory(),
        ];
    }
}