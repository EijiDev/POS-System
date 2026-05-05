<?php

namespace App\Exports\Sheets;

use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class SummarySheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(private int $days) {}

    public function title(): string { return 'Summary'; }

    public function array(): array
    {
        $from     = now()->subDays($this->days - 1)->startOfDay();
        $prevFrom = now()->subDays($this->days * 2 - 1)->startOfDay();
        $prevTo   = now()->subDays($this->days)->endOfDay();

        $revenue     = (float) Order::where('created_at', '>=', $from)->sum('total');
        $prevRevenue = (float) Order::whereBetween('created_at', [$prevFrom, $prevTo])->sum('total');
        $revenueChange = $prevRevenue > 0 ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

        $orderCount = Order::where('created_at', '>=', $from)->count();
        $avgOrder   = $orderCount > 0 ? round($revenue / $orderCount, 2) : 0;
        $prevCount  = Order::whereBetween('created_at', [$prevFrom, $prevTo])->count();
        $prevAvg    = $prevCount > 0 ? round($prevRevenue / $prevCount, 2) : 0;
        $avgChange  = $prevAvg > 0 ? round((($avgOrder - $prevAvg) / $prevAvg) * 100, 1) : 0;

        $costOfGoods = (float) OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', $from))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->sum(DB::raw('order_items.quantity * products.cost'));
        $grossProfit = $revenue - $costOfGoods;
        $margin      = $revenue > 0 ? round(($grossProfit / $revenue) * 100, 1) : 0;

        $expenses = (float) Expense::where('date', '>=', $from->toDateString())->sum('amount');

        $expBreakdown = Expense::where('date', '>=', $from->toDateString())
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')->orderByDesc('total')->get()
            ->map(fn($e) => $e->category . ': ₱' . number_format($e->total, 2))
            ->implode(', ');

        return [
            ['BrewPOS – Analytics & Reports'],
            ['Period: Last ' . $this->days . ' Days', 'Generated: ' . now()->format('F d, Y')],
            [],
            ['METRIC', 'VALUE', 'VS PREVIOUS PERIOD'],
            ['Total Revenue',    '₱' . number_format($revenue, 2),     ($revenueChange >= 0 ? '+' : '') . $revenueChange . '%'],
            ['Avg. Order Value', '₱' . number_format($avgOrder, 2),    ($avgChange >= 0 ? '+' : '') . $avgChange . '%'],
            ['Total Orders',     $orderCount,                           ''],
            ['Gross Profit',     '₱' . number_format($grossProfit, 2), $margin . '% Margin'],
            ['Total Expenses',   '₱' . number_format($expenses, 2),    $expBreakdown],
            ['Net Profit',       '₱' . number_format($grossProfit - $expenses, 2), ''],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '4a2c1a']]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4a2c1a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 22, 'C' => 35];
    }
}
