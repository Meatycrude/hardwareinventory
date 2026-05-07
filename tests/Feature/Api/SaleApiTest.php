<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Sale;

class SaleApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;
    public function test_can_create_sale_via_api(): void
{
    $product = Product::factory()->create([
        'selling_price' => 100,
        'stock_quantity' => 20,
    ]);

    $response = $this->postJson('/api/sales', [

        'payment_method' => 'cash',

        'items' => [
            [
                'product_id' => $product->id,
                'quantity' => 2,
            ]
        ]
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('sales', [
        'payment_method' => 'cash',
        'total_amount' => 200,
    ]);
}
public function test_can_get_all_sales(): void
{
    Sale::factory()->count(3)->create();

    $response = $this->getJson('/api/sales');

    $response->assertOk()
             ->assertJsonCount(3);
}
public function test_can_get_single_sale(): void
{
    $sale = Sale::factory()->create();

    $response = $this->getJson(
        "/api/sales/{$sale->id}"
    );

    $response->assertOk();
}
}
