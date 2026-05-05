<?php

namespace Database\Factories;

use App\Models\SaleItem;
use App\Models\product;
use App\Models\sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleItem>
 */
class SaleItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sale_id' => sale::factory(),
            'product_id' => product::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->randomFloat(2, 1, 100),
            'subtotal' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}
