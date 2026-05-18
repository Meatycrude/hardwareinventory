<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable('product_id', 'movement_type', 'quantity')]

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
