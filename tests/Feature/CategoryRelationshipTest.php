<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;         


class CategoryRelationshipTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_category_has_many_products(): void
{
    $category = Category::factory()->create();

    \App\Models\Product::factory()->count(3)->create([
        'category_id' => $category->id,
    ]);

    $this->assertCount(
        3,
        $category->products
    );
}
}
