<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SaleService;
use Illuminate\Http\Request;
use App\Models\Sale;

class SaleController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

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
}
