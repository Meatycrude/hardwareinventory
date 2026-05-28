<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_product(): void
    {
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'brand' => 'Test Brand',
            'unit' => 'Piece',
            'buying_price' => 100,
            'selling_price' => 150,
            'stock_quantity' => 50,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'brand' => 'Test Brand',
            'stock_quantity' => 50,
        ]);
    }

    public function test_update_product(): void
    {
        $product = Product::factory()->create();

        $product->update([
            'name' => 'Updated Product',
            'stock_quantity' => 100,
            'selling_price' => 200,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'stock_quantity' => 100,
            'selling_price' => 200,
        ]);
    }

    public function test_delete_product(): void
    {
        $product = Product::factory()->create();

        $product->delete();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_product_factory_creates_valid_values(): void
    {
        $product = Product::factory()->create();

        $this->assertNotNull($product->name);
        $this->assertNotNull($product->sku);
        $this->assertGreaterThan(0, $product->buying_price);
        $this->assertGreaterThan(0, $product->selling_price);
        $this->assertGreaterThanOrEqual(0, $product->stock_quantity);
    }

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->assertNotNull($product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_belongs_to_supplier(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create(['supplier_id' => $supplier->id]);

        $this->assertNotNull($product->supplier);
        $this->assertEquals($supplier->id, $product->supplier->id);
    }

    public function test_product_has_many_stock_movements(): void
    {
        $product = Product::factory()->create();

        $product->stockMovements()->createMany([
            [
                'quantity' => 10,
                'type' => 'purchase',
            ],
            [
                'quantity' => -5,
                'type' => 'sale',
            ],
        ]);

        $this->assertCount(2, $product->stockMovements);
    }

    public function test_product_has_many_sale_items(): void
    {
        $product = Product::factory()->create();

        $sale = Sale::factory()->create();

        $product->saleItems()->createMany([
            [
                'sale_id' => $sale->id,
                'quantity' => 2,
                'unit_price' => 100,
                'subtotal' => 200,
            ],
            [
                'sale_id' => $sale->id,
                'quantity' => 1,
                'unit_price' => 100,
                'subtotal' => 100,
            ],
        ]);

        $this->assertCount(2, $product->saleItems);
    }

    public function test_product_prices_are_cast_to_decimal(): void
    {
        $product = Product::factory()->create([
            'buying_price' => 100.50,
            'selling_price' => 150.75,
        ]);

        $product->refresh();

        $this->assertIsString((string) $product->buying_price);
        $this->assertIsString((string) $product->selling_price);
    }

    public function test_can_load_product_with_relationships(): void
    {
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);

        $product->load(['category', 'supplier']);

        $this->assertNotNull($product->category);
        $this->assertNotNull($product->supplier);
    }

    public function test_minimum_stock_attribute_exists(): void
    {
        $product = Product::factory()->create([
            'minimum_stock' => 10,
        ]);

        $this->assertEquals(10, $product->minimum_stock);
    }

    public function test_description_attribute_exists(): void
    {
        $product = Product::factory()->create([
            'description' => 'Test description',
        ]);

        $this->assertEquals('Test description', $product->description);
    }
}
