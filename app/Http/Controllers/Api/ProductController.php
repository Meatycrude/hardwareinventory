<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'buying_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['sku'] = 'PROD-'.strtoupper(Str::random(8));

        $product = Product::create($validated);

        $product->load(['category', 'supplier']);

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

    public function restock(Request $request, Product $product)
{
    $validated = $request->validate([
        'quantity' => 'required|integer|min:1',
        'buying_price' => 'required|numeric|min:0',
    ]);

    
    return \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $product) {
        
        $product->increment('stock_quantity', $validated['quantity']);
        $product->update([
            'buying_price' => $validated['buying_price']
        ]);

        $movement = new \App\Models\StockMovement();
        $movement->product_id    = $product->id;
        $movement->quantity      = $validated['quantity'];
        $movement->movement_type = 'purchase';
        $movement->save();

        $product->load(['category', 'supplier']);

        return response()->json($product, 200);
    });
}

}
