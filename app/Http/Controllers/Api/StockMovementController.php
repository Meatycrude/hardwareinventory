<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;

class StockMovementController extends Controller
{
    public function index()
    {
        $stockMovements = StockMovement::all();

        return response()->json($stockMovements);
    }

    public function productMovements($productId)
    {
        $stockMovements = StockMovement::where('product_id', $productId)->get();

        return response()->json($stockMovements);
    }
}
