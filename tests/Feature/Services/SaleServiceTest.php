<?php

namespace Tests\Feature\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_sale(): void
    {
        $product = Product::factory()->create([
            'selling_price' => 100,
            'stock_quantity' => 20,
        ]);

        $service = new SaleService;

        $sale = $service->createSale([
            'payment_method' => 'cash',

            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $this->assertInstanceOf(
            Sale::class,
            $sale
        );

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'payment_method' => 'cash',
            'total_amount' => 200,
        ]);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100,
            'subtotal' => 200,
        ]);
    }

    public function test_sale_reduces_stock(): void
    {
        $product = Product::factory()->create([
            'selling_price' => 50,
            'stock_quantity' => 10,
        ]);

        $service = new SaleService;

        $service->createSale([
            'payment_method' => 'cash',

            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 7,
        ]);
    }

    public function test_sale_creates_stock_movement(): void
    {
        $product = Product::factory()->create([
            'selling_price' => 75,
            'stock_quantity' => 10,
        ]);

        $service = new SaleService;

        $service->createSale([
            'payment_method' => 'cash',

            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => -2,
        ]);
    }

    public function test_cannot_sell_more_than_available_stock(): void
    {
        $this->expectException(\Exception::class);

        $product = Product::factory()->create([
            'selling_price' => 100,
            'stock_quantity' => 1,
        ]);

        $service = new SaleService;

        $service->createSale([
            'payment_method' => 'cash',

            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
        ]);
    }

    public function test_sale_requires_items(): void
    {
        $response = $this->postJson('/api/sales', [
            'payment_method' => 'cash',
            'items' => [],
        ]);

        $response->assertUnprocessable();
    }

    public function test_api_cannot_sell_more_than_available_stock(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 1,
            'selling_price' => 100,
        ]);

        $response = $this->postJson('/api/sales', [

            'payment_method' => 'cash',

            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                ],
            ],
        ]);

        $response->assertStatus(422);
    }
}
