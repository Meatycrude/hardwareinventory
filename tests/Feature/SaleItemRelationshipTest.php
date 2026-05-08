<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleItemRelationshipTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_sale_item_belongs_to_product(): void
    {
        $product = Product::factory()->create();

        $saleItem = SaleItem::factory()->create([
            'product_id' => $product->id,
        ]);

        $this->assertInstanceOf(
            Product::class,
            $saleItem->product
        );
    }
}
