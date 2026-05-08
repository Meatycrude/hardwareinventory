<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_factory_creates_product_values(): void
    {
        $product = Product::factory()->create();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => $product->category_id,
            'sku' => $product->sku,
            'name' => $product->name,
            'brand' => $product->brand,
            'unit' => $product->unit,
            'buying_price' => $product->buying_price,
            'selling_price' => $product->selling_price,
            'stock_quantity' => $product->stock_quantity,
            'minimum_stock' => $product->minimum_stock,
            'description' => $product->description,
        ]);

        $this->assertNotNull($product->category_id);
        $this->assertDatabaseHas('categories', [
            'id' => $product->category_id,
        ]);
    }
}
