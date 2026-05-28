<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/profile');

        $response->assertOk()
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('old-password'),
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/profile/password', [
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertOk();
    }

    public function test_admin_can_create_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Cashier User',
                'email' => 'cashier@test.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'cashier',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'cashier@test.com',
            'role' => 'cashier',
        ]);
    }

    public function test_non_admin_cannot_create_user(): void
    {
        $cashier = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this
            ->actingAs($cashier, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'User',
                'email' => 'user@test.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => 'cashier',
            ]);

        $response->assertForbidden();
    }
}
