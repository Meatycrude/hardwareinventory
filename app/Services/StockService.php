<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function addStock(
        Product $product,
        int $quantity,
        ?string $reference = null,
        ?string $notes = null
    ): Product {

        return DB::transaction(function () use (
            $product,
            $quantity,
            $reference,
            $notes
        ) {

            $product->increment('stock_quantity', $quantity);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'purchase',
                'quantity' => $quantity,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            return $product->fresh();
        });
    }

    public function reduceStock(
        Product $product,
        int $quantity,
        ?string $reference = null,
        ?string $notes = null
    ): Product {

        if ($product->stock_quantity < $quantity) {
            throw new Exception(
                'Insufficient stock.'
            );
        }

        return DB::transaction(function () use (
            $product,
            $quantity,
            $reference,
            $notes
        ) {

            $product->decrement(
                'stock_quantity',
                $quantity
            );

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => -$quantity,
                'reference' => $reference,
                'notes' => $notes,
            ]);

            return $product->fresh();
        });
    }
}
