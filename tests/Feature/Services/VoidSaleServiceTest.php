<?php

namespace Tests\Feature\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\VoidSaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoidSaleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_void_a_sale(): void
    {
        $sale = Sale::factory()->create([
            'status' => 'completed',
        ]);

        $service = new VoidSaleService();

        $voidedSale = $service->void($sale);

        $this->assertEquals('cancelled', $voidedSale->status);
        $this->assertNotNull($voidedSale->cancelled_at);
    }

    public function test_voiding_sale_restores_stock(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 8,
        ]);

        $sale = Sale::factory()->create([
            'status' => 'completed',
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $service = new VoidSaleService();

        $service->void($sale);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 10,
        ]);
    }

    public function test_voiding_sale_creates_stock_movement(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 8,
        ]);

        $sale = Sale::factory()->create([
            'status' => 'completed',
        ]);

        SaleItem::factory()->create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $service = new VoidSaleService();

        $service->void($sale);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'purchase',
            'quantity' => 2,
            'reference' => "VOID-SALE-{$sale->id}",
        ]);
    }

    public function test_cannot_void_sale_twice(): void
    {
        $this->expectException(\Exception::class);

        $sale = Sale::factory()->create([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $service = new VoidSaleService();

        $service->void($sale);
    }
    public function test_voiding_sale_creates_audit_log(): void
{
    $user = \App\Models\User::factory()->create([
        'role' => 'admin',
    ]);

    $this->actingAs($user, 'sanctum');

    $product = Product::factory()->create([
        'stock_quantity' => 8,
    ]);

    $sale = Sale::factory()->create([
        'status' => 'completed',
    ]);

    SaleItem::factory()->create([
        'sale_id' => $sale->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $service = new VoidSaleService();

    $service->void($sale);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'action' => 'sale.cancelled',
    ]);
}
}