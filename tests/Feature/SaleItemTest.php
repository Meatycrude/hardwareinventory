<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_sale_item(): void
    {
        $sale = Sale::factory()->create();
        $product = Product::factory()->create();

        $item = SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseHas('sale_items', [
            'id' => $item->id,
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'subtotal' => $item->subtotal,
        ]);

        $this->assertNotNull($item->sale_id);
        $this->assertNotNull($item->product_id);
        $this->assertNotNull($item->quantity);
        $this->assertNotNull($item->unit_price);
        $this->assertNotNull($item->subtotal);
    }

    public function test_update_sale_item(): void
    {
        $sale = Sale::factory()->create();
        $product = Product::factory()->create();

        $item = SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
        ]);

        $item->update([
            'quantity' => 5,
            'unit_price' => 10.00,
            'subtotal' => 50.00,
        ]);

        $this->assertDatabaseHas('sale_items', [
            'id' => $item->id,
            'quantity' => 5,
            'unit_price' => 10.00,
            'subtotal' => 50.00,
        ]);
    }
}
