<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

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
    public function salesTrend()
{
    $sales = Sale::query()
        ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
        ->where('status', 'completed')
        ->whereDate('created_at', '>=', now()->subDays(6))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get();

    return response()->json($sales);
}
}
