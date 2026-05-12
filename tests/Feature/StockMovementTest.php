<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase , WithFaker;

    public function test_stock_movement(): void
    {
        $product = Product::factory()->create();
        $stockMovement = StockMovement::factory()->create(['product_id' => $product->id]);
        $this->assertDatabaseHas('stock_movements', [

            'id' => $stockMovement->id,
            'product_id' => $stockMovement->product_id,
            'type' => $stockMovement->type,
            'quantity' => $stockMovement->quantity,
            'reference' => $stockMovement->reference,
        ]);
    }

    public function test_can_get_all_stock_movements(): void
    {
        StockMovement::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(
            '/api/stock-movements'
        );

        $response->assertOk()
            ->assertJsonCount(5);
    }

    public function test_can_get_product_stock_movements(): void
    {
        $product = Product::factory()
            ->create();

        StockMovement::factory()
            ->count(3)
            ->create([
                'product_id' => $product->id,
            ]);

        $response = $this->getJson(
            "/api/products/{$product->id}/movements"
        );

        $response->assertOk()
            ->assertJsonCount(3);
    }
}
