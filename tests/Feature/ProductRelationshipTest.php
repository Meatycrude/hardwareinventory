<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(
            Category::class,
            $product->category
        );
    }

    public function test_product_belongs_to_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
        ]);

        $this->assertInstanceOf(
            Supplier::class,
            $product->supplier
        );
    }

    public function test_product_has_many_stock_movements(): void
    {
        $product = Product::factory()->create();

        $this->assertTrue(
            method_exists($product, 'stockMovements')
        );
    }

    public function test_product_has_many_sale_items(): void
    {
        $product = Product::factory()->create();

        $this->assertTrue(
            method_exists($product, 'saleItems')
        );
    }
}