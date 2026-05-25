<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AutheticationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_api_login_route(): void
    {
        // Arrange
        Mail::fake();
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password',
        ];

        // Act

        $response = $this->postJson(route('api.login'), $payload);

        // Assertion - Login should return requires_2fa
        $response->assertJson(fn (AssertableJson $assertableJson) => $assertableJson
            ->has('requires_2fa')
            ->where('requires_2fa', true)
            ->etc()
        );
    }

    public function test_api_logout_route(): void
    {
        // Arrange
        Mail::fake();
        $user = User::factory()->create();

        $payload = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $loginResponse = $this->postJson(route('api.login'), $payload);

        // Get the 2FA code and verify
        $code = $user->fresh()->two_factor_code;

        $verifyResponse = $this->postJson(route('api.verify-2fa'), [
            'email' => $user->email,
            'code' => $code,
        ]);

        $token = $verifyResponse->json('access_token');

        // Act

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson(route('api.logout'));

        // Assertion

        $response->assertStatus(204);
    }
}
