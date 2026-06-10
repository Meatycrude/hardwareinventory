<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleReceiptApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_sale_receipt_details(): void
    {
        $sale = Sale::factory()->create([
            'invoice_number' => 'INV-001',
            'payment_method' => 'cash',
            'total_amount' => 1500,
        ]);

        $product = Product::factory()->create([
            'name' => 'Simba Cement',
            'selling_price' => 750,
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 750,
            'subtotal' => 1500,
        ]);

        $response = $this->getJson("/api/sales/{$sale->id}/receipt");

        $response->assertOk();

        $response->assertJsonStructure([
            'business' => [
                'name',
                'address',
                'phone',
            ],
            'receipt' => [
                'invoice_number',
                'payment_method',
                'total_amount',
                'date',
                'items' => [
                    [
                        'product_name',
                        'sku',
                        'quantity',
                        'unit_price',
                        'subtotal',
                    ],
                ],
            ],
        ]);

    }

    public function test_receipt_returns_all_sale_items(): void
    {
        $sale = Sale::factory()->create();

        $productA = Product::factory()->create([
            'name' => 'Cement',
        ]);

        $productB = Product::factory()->create([
            'name' => 'Paint',
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $productA->id,
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $productB->id,
        ]);

        $response = $this->getJson(
            "/api/sales/{$sale->id}/receipt"
        );

        $response->assertOk();

        $this->assertCount(
            2,
            $response->json('receipt.items')
        );
    }

    public function test_receipt_returns_404_for_unknown_sale(): void
    {
        $response = $this->getJson(
            '/api/sales/99999/receipt'
        );

        $response->assertNotFound();
    }

    public function test_receipt_returns_printable_receipt_data(): void
    {
        $sale = Sale::factory()->create([
            'invoice_number' => 'INV-PRINT-001',
            'payment_method' => 'cash',
            'total_amount' => 2000,
        ]);

        $product = Product::factory()->create([
            'name' => 'Simba Cement',
            'sku' => 'CEM-001',
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 1000,
            'subtotal' => 2000,
        ]);

        $response = $this->getJson("/api/sales/{$sale->id}/receipt");

        $response->assertOk();

        $response->assertJsonStructure([
            'business' => [
                'name',
                'address',
                'phone',
            ],
            'receipt' => [
                'invoice_number',
                'payment_method',
                'total_amount',
                'date',
                'items' => [
                    [
                        'product_name',
                        'sku',
                        'quantity',
                        'unit_price',
                        'subtotal',
                    ],
                ],
            ],
        ]);

        $response->assertJsonPath('business.name', 'Kaura Hardware');
        $response->assertJsonPath('receipt.invoice_number', 'INV-PRINT-001');
        $response->assertJsonPath('receipt.items.0.product_name', 'Simba Cement');
    }

    public function test_receipt_total_matches_sale_total(): void
    {
        $sale = Sale::factory()->create([
            'total_amount' => 5000,
        ]);

        $response = $this->getJson(
            "/api/sales/{$sale->id}/receipt"
        );

        $response->assertOk();

        $this->assertEquals(
            5000,
            $response->json('receipt.total_amount')
        );
    }
}
