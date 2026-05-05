<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\stockMovement;
use App\Models\product;

class StockMovementTest extends TestCase

{
    use RefreshDatabase , WithFaker;
    /**
     * A basic feature test example.
     */
    public function test_stock_movement(): void
    {
       $product = product::factory()->create();
       $stockMovement = stockMovement::factory()->create(['product_id' => $product->id]);
        $this->assertDatabaseHas('stock_movements', [
            'id' => $stockMovement->id,
            'product_id' => $stockMovement->product_id,
            'type' => $stockMovement->type,
            'quantity' => $stockMovement->quantity,
            'reference' => $stockMovement->reference,
        ]); 
    }

    
}
