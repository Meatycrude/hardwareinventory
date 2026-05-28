<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {

        Category::insert([
            [
                'name' => 'Cement',
                'slug' => Str::slug('Cement'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nails',
                'slug' => Str::slug('Nails'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'slug' => Str::slug('Other'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // If your suppliers table has a similar restriction, use model creation factories or standard mapping instead:
        Supplier::insert([
            ['name' => 'Prime Distribution Co.', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alpha Supply Chain Inc.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
