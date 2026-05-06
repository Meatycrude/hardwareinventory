<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Supplier;

class SupplierRelationshipTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_supplier_has_many_products(): void
{
    $supplier = Supplier::factory()->create();

    \App\Models\Product::factory()->count(5)->create([
        'supplier_id' => $supplier->id,
    ]);

    $this->assertCount(
        5,
        $supplier->products
    );
}
}
