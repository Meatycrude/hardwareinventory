<?php

namespace Database\Factories;

use App\Models\sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => $this->faker->unique()->numerify('INV-#####'),
            'total_amount' => $this->faker->numberBetween(10, 99),
            'payment_method' => $this->faker->randomElement(['cash', 'mpesa', 'bank', 'card']),
            'user_id' => null, 
     
        ];
    }
}
