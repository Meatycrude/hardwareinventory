<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_movement_belongs_to_product()
    {
        $product = Product::factory()->create();

        $movement = StockMovement::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(Product::class, $movement->product);
        $this->assertEquals($product->id, $movement->product->id);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
        ]);
    }
}
