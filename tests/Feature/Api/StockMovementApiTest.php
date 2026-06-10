<?php

namespace Tests\Feature\Api;

use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StockMovementApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_get_all_stock_movements(): void
    {
        $storekeeper = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        StockMovement::factory()
            ->count(5)
            ->create();

        $response = $this
            ->actingAs($storekeeper, 'sanctum')
            ->getJson('/api/stock-movements');

        $response->assertOk()
            ->assertJsonCount(5);
    }
}
