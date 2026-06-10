<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => $this->faker->randomElement([
                'purchase',
                'sale',
            ]),
            'quantity' => $this->faker->numberBetween(1, 100),
            'reference' => $this->faker->word(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
