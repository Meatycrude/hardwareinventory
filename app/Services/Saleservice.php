<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleService
{
    

public function createSale(array $data): Sale
{
    return DB::transaction(function () use ($data) {
        
        $invoiceNumber = 'INV-' . strtoupper(bin2hex(random_bytes(4)));

        
        $sale = Sale::create([
            'invoice_number' => $invoiceNumber, 
            'payment_method' => $data['payment_method'],
            'total_amount'   => 0,
        ]);

        $totalAmount = 0;

        foreach ($data['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            if ($product->stock_quantity < $item['quantity']) {
                throw new \Exception("Insufficient stock");
            }

            $subtotal = $product->selling_price * $item['quantity'];
            $totalAmount += $subtotal;

            $sale->items()->create([
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->selling_price,
                'subtotal'   => $subtotal,
            ]);

            $product->decrement('stock_quantity', $item['quantity']);

            $product->stockMovements()->create([
                'type'     => 'sale',
                'quantity' => -$item['quantity'],
            ]);
        }

        $sale->update(['total_amount' => $totalAmount]);

        return $sale;
    });
}

}
