<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function stats(Request $request)
    {
        $days  = min((int) $request->input('days', 30), 365);
        $from  = now()->subDays($days - 1)->startOfDay();
        $prevFrom = now()->subDays($days * 2 - 1)->startOfDay();
        $prevTo   = now()->subDays($days)->endOfDay();

        // Revenue
        $revenue     = Order::where('created_at', '>=', $from)->sum('total');
        $prevRevenue = Order::whereBetween('created_at', [$prevFrom, $prevTo])->sum('total');
        $revenueChange = $prevRevenue > 0 ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1) : null;

        // Avg order value
        $orderCount = Order::where('created_at', '>=', $from)->count();
        $avgOrder   = $orderCount > 0 ? $revenue / $orderCount : 0;
        $prevCount  = Order::whereBetween('created_at', [$prevFrom, $prevTo])->count();
        $prevAvg    = $prevCount > 0 ? $prevRevenue / $prevCount : 0;
        $avgChange  = $prevAvg > 0 ? round((($avgOrder - $prevAvg) / $prevAvg) * 100, 1) : null;

        // Expenses
        $expenses = Expense::where('date', '>=', $from->toDateString())->sum('amount');

        // Gross profit (revenue - cost of items sold)
        $costOfGoods = OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', $from))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('order_items.quantity * products.cost'));
        $grossProfit = $revenue - $costOfGoods;
        $margin      = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0;

        // Revenue over time
        $revenueChart = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', $from)
            ->groupBy('date')->orderBy('date')->get();

        $chartMap = $revenueChart->keyBy('date');
        $chartData = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartData->push(['date' => $date, 'total' => $chartMap->has($date) ? (float)$chartMap[$date]->total : 0]);
        }

        // Popular items
        $popularItems = OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', $from))
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(8)->get();

        // Sales by category
        $byCategory = OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', $from))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('products.category')
            ->orderByDesc('total_revenue')
            ->get();

        // Expense breakdown
        $expenseBreakdown = Expense::where('date', '>=', $from->toDateString())
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')->orderByDesc('total')->get();

        // Orders for table — limit 500, items via single grouped query
        $orderIds = Order::where('created_at', '>=', $from)
            ->orderByDesc('id')->limit(500)->pluck('id');

        $itemsByOrder = OrderItem::whereIn('order_id', $orderIds)
            ->select('order_id', DB::raw("GROUP_CONCAT(CONCAT(product_name, ' \u00d7', quantity) SEPARATOR ', ') as summary"))
            ->groupBy('order_id')
            ->pluck('summary', 'order_id');

        $orders = Order::select('id', 'order_number', 'created_at', 'total', 'payment_method')
            ->whereIn('id', $orderIds)
            ->orderByDesc('id')
            ->get()
            ->map(fn($o) => [
                'order_number'  => $o->order_number,
                'date'          => $o->created_at->format('M d, Y H:i'),
                'items_summary' => $itemsByOrder[$o->id] ?? '',
                'total'         => $o->total,
                'payment'       => $o->payment_method,
            ]);

        return response()->json([
            'revenue'          => $revenue,
            'revenueChange'    => $revenueChange,
            'avgOrder'         => $avgOrder,
            'avgChange'        => $avgChange,
            'grossProfit'      => $grossProfit,
            'margin'           => $margin,
            'expenses'         => $expenses,
            'expenseBreakdown' => $expenseBreakdown,
            'chartData'        => $chartData,
            'popularItems'     => $popularItems,
            'byCategory'       => $byCategory,
            'orderCount'       => $orderCount,
            'orders'           => $orders,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $days     = min((int) $request->input('days', 30), 365);
        $filename = 'brewpos-report-' . now()->format('Y-m-d') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new OrdersExport($days), $filename);
    }
}
