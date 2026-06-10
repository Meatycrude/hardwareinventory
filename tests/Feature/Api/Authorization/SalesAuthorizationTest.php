<?php

namespace Tests\Feature\Api\Authorization;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_sale(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $product = Product::factory()->create([
            'stock_quantity' => 10,
            'selling_price' => 100,
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/sales', [
                'payment_method' => 'cash',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ],
                ],
            ]);

        $response->assertCreated();
    }

    public function test_cashier_can_create_sale(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $product = Product::factory()->create([
            'stock_quantity' => 10,
            'selling_price' => 100,
        ]);

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->postJson('/api/sales', [
                'payment_method' => 'cash',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ],
                ],
            ]);

        $response->assertCreated();
    }

    public function test_storekeeper_cannot_create_sale(): void
    {
        $storekeeper = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        $product = Product::factory()->create([
            'stock_quantity' => 10,
            'selling_price' => 100,
        ]);

        $response = $this
            ->actingAs($storekeeper, 'sanctum')
            ->postJson('/api/sales', [
                'payment_method' => 'cash',
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 2,
                    ],
                ],
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_view_sales(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/sales');

        $response->assertOk();
    }

    public function test_cashier_can_view_sales(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->getJson('/api/sales');

        $response->assertOk();
    }

    public function test_storekeeper_can_view_sales(): void
    {
        $storekeeper = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        $response = $this
            ->actingAs($storekeeper, 'sanctum')
            ->getJson('/api/sales');

        $response->assertOk();
    }
}
