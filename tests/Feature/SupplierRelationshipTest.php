<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierRelationshipTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_supplier_has_many_products(): void
    {
        $supplier = Supplier::factory()->create();

        Product::factory()->count(5)->create([
            'supplier_id' => $supplier->id,
        ]);

        $this->assertCount(
            5,
            $supplier->products
        );
    }
}
