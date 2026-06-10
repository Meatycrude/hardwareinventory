<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\StockMovement;
use Exception;
use Illuminate\Support\Facades\DB;

class VoidSaleService
{
    public function void(Sale $sale): Sale
    {
        if ($sale->status === 'cancelled') {
            throw new Exception('Sale has already been cancelled.');
        }

        return DB::transaction(function () use ($sale) {
            $sale->load('items.product');

            foreach ($sale->items as $item) {
                $item->product->increment(
                    'stock_quantity',
                    $item->quantity
                );

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'type' => 'purchase',
                    'quantity' => $item->quantity,
                    'reference' => "VOID-SALE-{$sale->id}",
                    'notes' => "Stock restored after voiding sale {$sale->invoice_number}",
                ]);
            }

            $sale->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            AuditService::log(
                auth()->id(),
                'sale.cancelled',
                "Cancelled sale {$sale->invoice_number}",
                [
                    'sale_id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                ]
            );

            return $sale->fresh();
        });
    }
}
