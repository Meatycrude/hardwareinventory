<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([

            'total_products' => Product::count(),

            'cancelled_sales' => Sale::where('status', 'cancelled')->count(),

            'total_categories' => Category::count(),

            'total_suppliers' => Supplier::count(),

            'total_sales' => Sale::where('status', 'completed')->sum('total_amount'),

            'today_sales' => Sale::where('status', 'completed')->whereDate(
                'created_at',
                today()
            )->sum('total_amount'),

            'low_stock_products' => Product::whereColumn(
                'stock_quantity',
                '<=',
                'minimum_stock'
            )->count(),
        ]);
    }
  

public function recentActivity()
{
    return response()->json(
        AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get()
    );
}
}
