<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $validated['sku'] = 'PROD-' . strtoupper(Str::random(8));

        $product = Product::create($validated);

        AuditService::log(
            auth()->id(),
            'product.created',
            "Created product {$product->name}",
            [
                'product_id' => $product->id,
                'sku' => $product->sku,
            ]
        );

        $product->load(['category', 'supplier']);

        return response()->json($product, 201);
    }

    public function index()
    {
        $products = Product::with(['category', 'supplier'])->get();

        return response()->json($products, 200);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier']);

        return response()->json($product, 200);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|unique:products,sku,' . $product->id,
            'name' => 'required|string',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $oldData = $product->only([
            'sku',
            'name',
            'stock_quantity',
        ]);

        $product->update($validated);

        AuditService::log(
            auth()->id(),
            'product.updated',
            "Updated product {$product->name}",
            [
                'product_id' => $product->id,
                'old' => $oldData,
                'new' => $product->only([
                    'sku',
                    'name',
                    'stock_quantity',
                ]),
            ]
        );

        return response()->json($product, 200);
    }

    public function destroy(Product $product)
    {
        $productId = $product->id;
        $productName = $product->name;

        $product->delete();

        AuditService::log(
            auth()->id(),
            'product.deleted',
            "Deleted product {$productName}",
            [
                'product_id' => $productId,
            ]
        );

        return response()->json(null, 204);
    }

    public function restock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'buying_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $product) {
            $product->increment('stock_quantity', $validated['quantity']);

            $product->update([
                'buying_price' => $validated['buying_price'],
            ]);

            $movement = new StockMovement;
            $movement->product_id = $product->id;
            $movement->quantity = $validated['quantity'];
            $movement->type = 'purchase';
            $movement->save();

            AuditService::log(
                auth()->id(),
                'product.restocked',
                "Restocked product {$product->name}",
                [
                    'product_id' => $product->id,
                    'quantity' => $validated['quantity'],
                    'buying_price' => $validated['buying_price'],
                    'stock_movement_id' => $movement->id,
                ]
            );

            $product->load(['category', 'supplier']);

            return response()->json($product, 200);
        });
    }
}