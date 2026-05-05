<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_creating_a_new_category(): void
    {

        $attributes = [
            'name' => 'Test Category',
        ];

        $category = Category::create($attributes);

        $this->assertDatabaseHas(Category::class, [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

    public function test_creating_category_from_factory(): void
    {
        $category = Category::factory()->create();


        $this->assertDatabaseHas(Category::class, [
            'name' => $category->name,
            'slug' => $category->slug,
        ]);
    }
}
