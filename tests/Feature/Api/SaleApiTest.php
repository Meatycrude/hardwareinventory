<?php

namespace Tests\Feature\Api;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaleApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_create_sale_via_api(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $product = Product::factory()->create([
            'selling_price' => 100,
            'stock_quantity' => 20,
            'minimum_stock' => 5,
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

        $this->assertDatabaseHas('sales', [
            'payment_method' => 'cash',
            'total_amount' => 200,
        ]);
    }

    public function test_can_get_all_sales(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        Sale::factory()->count(3)->create();

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->getJson('/api/sales');

        $response->assertOk()
            ->assertJsonCount(3);
    }

    public function test_can_get_single_sale(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $sale = Sale::factory()->create();

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->getJson("/api/sales/{$sale->id}");

        $response->assertOk();
    }
}