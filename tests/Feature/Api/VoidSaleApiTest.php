<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoidSaleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_void_sale(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

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

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson("/api/sales/{$sale->id}/void");

        $response->assertOk();

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_cashier_can_void_sale(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $sale = Sale::factory()->create([
            'status' => 'completed',
        ]);

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->postJson("/api/sales/{$sale->id}/void");

        $response->assertOk();
    }

    public function test_storekeeper_cannot_void_sale(): void
    {
        $storekeeper = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        $sale = Sale::factory()->create([
            'status' => 'completed',
        ]);

        $response = $this
            ->actingAs($storekeeper, 'sanctum')
            ->postJson("/api/sales/{$sale->id}/void");

        $response->assertForbidden();
    }

    public function test_cannot_void_sale_twice_via_api(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $sale = Sale::factory()->create([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson("/api/sales/{$sale->id}/void");

        $response->assertStatus(422);
    }
}
