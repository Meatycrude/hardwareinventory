<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories,name',
        ]);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function index()
    {

        return response()->json(Category::all(), 200);
    }

    public function show(Category $category)
    {

        return response()->json($category, 200);

    }
}
