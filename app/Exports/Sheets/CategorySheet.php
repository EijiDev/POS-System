<?php

namespace App\Exports\Sheets;

use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CategorySheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private int $days) {}

    public function title(): string { return 'Sales by Category'; }

    public function headings(): array
    {
        return ['Category', 'Items Sold', 'Revenue (₱)'];
    }

    public function collection()
    {
        $from = now()->subDays($this->days - 1)->startOfDay();

        return OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', $from))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(order_items.quantity) as total_qty'), DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue'))
            ->groupBy('products.category')
            ->orderByDesc('total_revenue')
            ->get()
            ->map(fn($c) => [
                $c->category,
                $c->total_qty,
                number_format($c->total_revenue, 2),
            ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4a2c1a']],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 14, 'C' => 16];
    }
}
