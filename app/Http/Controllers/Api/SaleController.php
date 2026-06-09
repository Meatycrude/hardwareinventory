<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\Request;
use App\Services\AuditService;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $saleService) {}

    public function store(Request $request)
    {

        $data = $request->validate([
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $sale = $this->saleService->createSale($data);

            return response()->json($sale, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function index()
    {

        $sales = Sale::with('items.product')->get();

        return response()->json($sales, 200);
    }

    public function show(Sale $sale)
    {

        return response()->json($sale->load('items.product'), 200);
    }
    public function receipt(Sale $sale)
{    
    AuditService::log(
        auth()->id(),
        'receipt.generated',
        "Generated receipt {$sale->invoice_number}",
        [
            'sale_id' => $sale->id,
            'invoice_number' => $sale->invoice_number,
        ]
    );

    $sale->load('items.product');

    return response()->json([
        'business' => [
            'name' => 'Kaura Hardware',
            'address' => 'Busia, Kenya',
            'phone' => '+254 715 698 160',
        ],

        'receipt' => [
            'invoice_number' => $sale->invoice_number,
            'payment_method' => $sale->payment_method,
            'total_amount' => $sale->total_amount,
            'date' => $sale->created_at->format('d M Y, h:i A'),

            'items' => $sale->items->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ];
            })->values(),
        ],
    ]);
}
}
