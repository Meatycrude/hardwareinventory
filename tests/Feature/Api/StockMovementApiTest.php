<?php

namespace Tests\Feature\Api;

use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StockMovementApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;

    public function test_can_get_all_stock_movements(): void
    {
        StockMovement::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(
            '/api/stock-movements'
        );

        $response->assertOk()
            ->assertJsonCount(5);
    }
}
