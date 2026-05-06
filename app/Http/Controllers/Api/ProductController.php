<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product; // Don't forget to import the Model
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
       
        $product = Product::create($request->all());

        
        return response()->json($product, 201);
    }
    public function val(Request $request)
{
    $validated = $request->validate([
        'sku'            => 'required|unique:products,sku',
        'name'           => 'required|string',
        'stock_quantity' => 'required|integer|min:0',
    ]);

    $product = Product::create($validated);

    return response()->json($product, 201);
}
}
