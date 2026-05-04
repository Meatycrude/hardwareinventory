<?php

namespace Tests\Feature\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_creating_a_new_user(): void
    {
        $attributes = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $password = $this->faker->password(minLength: 8),
        ];

        $user = User::query()->create($attributes);

        $this->assertNotNull($user);

        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseHas(User::class, Arr::except($attributes, ['password']));

        $this->assertTrue(Hash::check($password, $user->password));

    }

    public function test_creating_a_new_user_from_factory(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user);

        $this->assertInstanceOf(User::class, $user);

        $this->assertTrue(Hash::check('password', $user->password));
    }
}
