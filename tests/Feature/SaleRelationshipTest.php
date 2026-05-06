<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SaleRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_has_many_items()
    {
        $sale = Sale::factory()->create();

        SaleItem::factory()
            ->count(3)
            ->create([
                'sale_id' => $sale->id,
            ]);

        $sale->load('items');

        $this->assertCount(3, $sale->items);
    }

    public function test_sale_items_belong_to_sale()
    {
        $sale = Sale::factory()->create();

        $item = SaleItem::factory()->create([
            'sale_id' => $sale->id,
        ]);

        $this->assertEquals($sale->id, $item->sale->id);
        $this->assertInstanceOf(Sale::class, $item->sale);
    }

    public function test_sale_total_amount_casts_correctly()
    {
        $sale = Sale::factory()->create([
            'total_amount' => 1500.75,
        ]);

        $this->assertIsString($sale->total_amount); // because decimal cast returns string
        $this->assertEquals('1500.75', $sale->total_amount);
    }
}