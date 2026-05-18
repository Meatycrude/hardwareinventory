<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data): Sale
    {
        return DB::transaction(function () use ($data) {

            $invoiceNumber = 'INV-'.strtoupper(bin2hex(random_bytes(4)));

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'payment_method' => $data['payment_method'],
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $subtotal = $product->selling_price * $item['quantity'];
                $totalAmount += $subtotal;

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->selling_price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock_quantity', $item['quantity']);

                $movement = new StockMovement;
                $movement->product_id = $product->id;
                $movement->quantity = -$item['quantity'];
                $movement->movement_type = 'sale';
                $movement->save();
            }

            $sale->update(['total_amount' => $totalAmount]);

            return $sale->load('items.product');
        });
    }
}
