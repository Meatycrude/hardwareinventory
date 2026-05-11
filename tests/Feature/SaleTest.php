<?php

namespace Tests\Feature;

use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_sale(): void
    {
        $sale = Sale::factory()->create();
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
            'total_amount' => $sale->total_amount,
            'payment_method' => $sale->payment_method,
            'user_id' => $sale->user_id,
        ]);

    }

    public function test_update_sale(): void
    {
        $sale = Sale::factory()->create();

        $sale->update([
            'invoice_number' => 'INV-54321',
            'total_amount' => 150,
            'payment_method' => 'card',
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'invoice_number' => 'INV-54321',
            'total_amount' => 150,
            'payment_method' => 'card',
            'user_id' => null,
        ]);
    }

    public function test_delete_sale(): void
    {
        $sale = Sale::factory()->create();

        $sale->delete();

        $this->assertDatabaseMissing('sales', [
            'id' => $sale->id,
        ]);
    }

    public function test_sale_factory_creates_valid_values(): void
    {
        $sale = Sale::factory()->create();

        $this->assertNotNull($sale->invoice_number);
        $this->assertGreaterThan(0, $sale->total_amount);
        $this->assertContains($sale->payment_method, ['cash', 'mpesa', 'bank', 'card']);
    }
    public function test_can_get_sale_with_items(): void
{
    $sale = \App\Models\Sale::factory()->create();

    $product = \App\Models\Product::factory()->create();

    \App\Models\SaleItem::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->getJson(
        "/api/sales/{$sale->id}"
    );

    $response->assertOk();

    $response->assertJsonStructure([
        'id',
        'invoice_number',
        'total_amount',

        'items' => [
            [
                'id',
                'quantity',
                'unit_price',
                'subtotal',

                'product' => [
                    'id',
                    'name',
                    'sku',
                ]
            ]
        ]
    ]);
}
}
