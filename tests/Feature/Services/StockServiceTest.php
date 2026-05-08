<?php

namespace Tests\Feature\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_stock(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 10,
        ]);

        $service = new StockService;

        $service->addStock(
            product: $product,
            quantity: 5,
            reference: 'PURCHASE-001',
            notes: 'Supplier delivery'
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 5,
            'reference' => 'PURCHASE-001',
            'notes' => 'Supplier delivery',
        ]);
    }

    public function test_can_reduce_stock(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 20,
        ]);

        $service = new StockService;

        $service->reduceStock(
            product: $product,
            quantity: 5,
            reference: 'SALE-001',
            notes: 'Customer purchase'
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 15,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'sale',
            'quantity' => -5,
            'reference' => 'SALE-001',
            'notes' => 'Customer purchase',
        ]);
    }

    public function test_cannot_reduce_stock_below_zero(): void
    {
        $this->expectException(\Exception::class);

        $product = Product::factory()->create([
            'stock_quantity' => 2,
        ]);

        $service = new StockService;

        $service->reduceStock(
            product: $product,
            quantity: 5,
            reference: 'SALE-002',
            notes: 'Attempted oversell'
        );
    }

    public function test_creates_stock_movement_record(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 50,
        ]);

        $service = new StockService;

        $service->addStock(
            product: $product,
            quantity: 10,
            reference: 'PURCHASE-002',
            notes: 'New stock arrival'
        );

        $movement = StockMovement::first();

        $this->assertNotNull($movement);

        $this->assertEquals(
            $product->id,
            $movement->product_id
        );

        $this->assertEquals(
            'purchase',
            $movement->type
        );
    }
}
