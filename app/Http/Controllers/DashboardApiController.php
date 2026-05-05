<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function stats()
    {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $days      = min((int) request('days', 7), 30);

        // Total sales today
        $salesToday     = Order::whereDate('created_at', $today)->sum('total');
        $salesYesterday = Order::whereDate('created_at', $yesterday)->sum('total');
        $salesChange    = $salesYesterday > 0
            ? round((($salesToday - $salesYesterday) / $salesYesterday) * 100, 1)
            : null;

        // Orders today
        $ordersToday     = Order::whereDate('created_at', $today)->count();
        $ordersYesterday = Order::whereDate('created_at', $yesterday)->count();
        $ordersChange    = $ordersYesterday > 0
            ? round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 1)
            : null;

        // Best selling product (all time by sold count)
        $bestProduct = Product::orderByDesc('sold')->first();

        // Total active products
        $totalProducts   = Product::where('status', 'Active')->count();
        $totalCategories = Product::where('status', 'Active')->distinct('category')->count('category');

        // Sales chart — last N days
        $salesData = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as daily_total')
            )
            ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing days with 0
        $salesMap = $salesData->keyBy('date');
        $chartData = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartData->push([
                'date'        => $date,
                'daily_total' => $salesMap->has($date) ? $salesMap[$date]->daily_total : 0,
            ]);
        }

        // Top 5 products by sold count
        $topProducts = Product::orderByDesc('sold')->take(5)->get(['name', 'sold']);

        // Recent 5 transactions
        $recentOrders = Order::with('items')->orderByDesc('id')->take(5)->get()
            ->map(fn($o) => [
                'order_number' => $o->order_number,
                'items_summary'=> $o->items->map(fn($i) => $i->product_name . ' × ' . $i->quantity)->implode(', '),
                'total'        => $o->total,
                'status'       => 'Paid',
            ]);

        return response()->json([
            'totalSales'      => $salesToday,
            'salesChange'     => $salesChange,
            'ordersToday'     => $ordersToday,
            'ordersChange'    => $ordersChange,
            'bestProduct'     => $bestProduct?->name ?? '—',
            'bestProductSold' => $bestProduct?->sold ?? 0,
            'totalProducts'   => $totalProducts,
            'totalCategories' => $totalCategories,
            'salesData'       => $chartData,
            'topProducts'     => $topProducts,
            'recentOrders'    => $recentOrders,
        ]);
    }
}
