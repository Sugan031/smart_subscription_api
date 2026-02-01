<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
           'name' => fake()->word(),
            'price' => fake()->randomFloat(2,0,5000),
            'monthly_limit' => fake()->randomElement(['100','10000',null]),
            'is_unlimited' => fake()->boolean(),
        ];
    }
}
