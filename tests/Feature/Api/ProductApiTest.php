<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;

    public function test_can_create_product(): void
    {
        $this->withoutExceptionHandling();
        $category = Category::factory()->create();

        $response = $this->postJson('/api/products', [

            'category_id' => $category->id,

            'name' => 'Simba Cement',

            'sku' => 'SIM001',

            'unit' => 'Bag',

            'buying_price' => 650,

            'selling_price' => 720,

            'stock_quantity' => 50,

        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('products', [
            'sku' => 'SIM001',
        ]);
    }

    public function test_can_get_all_products(): void
    {
        $this->withoutExceptionHandling();
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonCount(5);
    }

    public function test_can_get_single_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertOk();
    }

    public function test_can_update_product(): void
    {

        $product = Product::factory()->create();

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Cement',
            'sku' => $product->sku,
            'stock_quantity' => $product->stock_quantity,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('products', [
            'name' => 'Updated Cement',
        ]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
