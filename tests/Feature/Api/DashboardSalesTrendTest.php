<?php

namespace Tests\Feature\Api;

use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSalesTrendTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_daily_sales_trend(): void
    {
        Sale::factory()->create([
            'status' => 'completed',
            'total_amount' => 1000,
            'created_at' => now()->subDays(2),
        ]);

        Sale::factory()->create([
            'status' => 'completed',
            'total_amount' => 500,
            'created_at' => now()->subDays(2),
        ]);

        Sale::factory()->create([
            'status' => 'completed',
            'total_amount' => 2000,
            'created_at' => now()->subDay(),
        ]);

        Sale::factory()->create([
            'status' => 'cancelled',
            'total_amount' => 9000,
            'created_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/dashboard/sales-trend');

        $response->assertOk();

        $response->assertJsonFragment([
            'revenue' => 1500,
        ]);

        $response->assertJsonFragment([
            'revenue' => 2000,
        ]);

        $response->assertJsonMissing([
            'revenue' => 9000,
        ]);
    }
}