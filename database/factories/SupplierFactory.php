<?php

namespace Database\Factories;

use App\Models\supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),

        ];
    }
}
