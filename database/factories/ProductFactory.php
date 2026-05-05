<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<product>
 */
class ProductFactory extends Factory
{
    protected $model = product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'supplier_id' => null,
            'name' => $this->faker->words(3, true),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'brand' => $this->faker->company(),
            'unit' => $this->faker->randomElement(['piece', 'unit', 'box', 'kg']),
            'buying_price' => $this->faker->randomFloat(2, 1, 1000),
            'selling_price' => $this->faker->randomFloat(2, 1, 1200),
            'stock_quantity' => $this->faker->numberBetween(0, 200),
            'minimum_stock' => $this->faker->numberBetween(1, 20),
            'description' => $this->faker->sentence(),
        ];
    }
}
