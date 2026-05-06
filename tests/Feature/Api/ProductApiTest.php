<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;

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
public function test_it_validates_required_fields()
{
    
    $response = $this->postJson('/api/products', []);

    
    $response->assertStatus(422);
    
    
    $response->assertJsonValidationErrors(['sku', 'name']);
}
}
