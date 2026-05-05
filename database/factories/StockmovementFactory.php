<?php

namespace Database\Factories;

use App\Models\stockmovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<stockmovement>
 */
class StockmovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->randomNumber(),
            'product_id' => $this->faker->randomNumber(),
            'type' => $this->faker->randomElement(['purchase', 'sale', 'damaged', 'returned', 'adjustment']),
            'quantity' => $this->faker->randomNumber(),
            'reference' => $this->faker->word(),
            'notes' => $this->faker->sentence(),

        ];
    }
}
