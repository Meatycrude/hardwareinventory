<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_get_dashboard_statistics(): void
    {
        $categories = Category::factory()->count(3)->create();

        $suppliers = Supplier::factory()->count(2)->create();

        Product::factory()->count(10)->create([
            'category_id' => $categories->random()->id,
            'supplier_id' => $suppliers->random()->id,
        ]);

        Sale::factory()->create([
            'total_amount' => 1000,
        ]);

        Sale::factory()->create([
            'total_amount' => 500,
        ]);

        $response = $this->getJson(
            '/api/dashboard/stats'
        );

        $response->assertOk();

        $response->assertJsonStructure([
            'total_products',
            'total_categories',
            'total_suppliers',
            'total_sales',
            'today_sales',
            'low_stock_products',
        ]);

        $response->assertJson([
            'total_products' => 10,
            'total_categories' => 3,
            'total_suppliers' => 2,
        ]);
    }
}
