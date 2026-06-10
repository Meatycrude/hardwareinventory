<?php

namespace Tests\Feature\Api\Authorization;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_stock_movements(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/stock-movements');

        $response->assertOk();
    }

    public function test_storekeeper_can_view_stock_movements(): void
    {
        $storekeeper = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        $response = $this
            ->actingAs($storekeeper, 'sanctum')
            ->getJson('/api/stock-movements');

        $response->assertOk();
    }

    public function test_cashier_cannot_view_stock_movements(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->getJson('/api/stock-movements');

        $response->assertForbidden();
    }

    public function test_cashier_cannot_restock_products(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $product = Product::factory()->create();

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->postJson("/api/products/{$product->id}/restock", [
                'quantity' => 10,
            ]);

        $response->assertForbidden();
    }
}
