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
}
