<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_can_create_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Cement',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('categories', [
            'name' => 'Cement',
        ]);
    }

    public function test_can_get_all_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertOk()->assertJsonCount(3);
    }

    public function test_can_get_single_category(): void
    {

        $category = Category::factory()->create(['name' => 'Electrical']);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertOk()
            ->assertJsonPath('name', 'Electrical');
    }
}
