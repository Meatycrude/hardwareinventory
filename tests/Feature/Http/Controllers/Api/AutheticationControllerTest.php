<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AutheticationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_api_login_route(): void
    {
        // Arrange
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password',
        ];

        // Act

        $response = $this->postJson(route('api.login'), $payload);

        // Assertion

        $response->assertJson(fn (AssertableJson $assertableJson) => $assertableJson->has('access_token')->etc());
    }

    public function test_api_logout_route(): void
    {
        // Arrange
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $loginResponse = $this->postJson(route('api.login'), $payload);

        $token = $loginResponse->json('access_token');

        // Act

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson(route('api.logout'));

        // Assertion

        $response->assertStatus(204);
    }
}
