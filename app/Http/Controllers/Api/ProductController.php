<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {

        $product = Product::create($request->all());

        return response()->json($product, 201);
    }

    public function index()
    {
        $products = Product::all();
        $products->load(['category', 'supplier']);

        return response()->json($products, 200);
    }

    public function show(Product $product)
    {

        return response()->json($product, 200);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([

            'sku' => 'required|unique:products,sku,'.$product->id,
            'name' => 'required|string',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json($product, 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
