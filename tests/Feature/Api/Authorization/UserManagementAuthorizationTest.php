<?php

namespace Tests\Feature\Api\Authorization;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        User::factory()->count(3)->create();

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertOk();
    }

    public function test_cashier_cannot_view_users(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->getJson('/api/users');

        $response->assertForbidden();
    }

    public function test_storekeeper_cannot_view_users(): void
    {
        $storekeeper = User::factory()->create([
            'role' => 'storekeeper',
        ]);

        $response = $this
            ->actingAs($storekeeper, 'sanctum')
            ->getJson('/api/users');

        $response->assertForbidden();
    }
}