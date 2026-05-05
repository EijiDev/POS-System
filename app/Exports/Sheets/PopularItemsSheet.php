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

class PopularItemsSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private int $days) {}

    public function title(): string { return 'Popular Items'; }

    public function headings(): array
    {
        return ['Rank', 'Product Name', 'Units Sold', 'Revenue (₱)'];
    }

    public function collection()
    {
        $from = now()->subDays($this->days - 1)->startOfDay();

        return OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', $from))
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->get()
            ->map(fn($item, $i) => [
                $i + 1,
                $item->product_name,
                $item->total_qty,
                number_format($item->total_revenue, 2),
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
        return ['A' => 8, 'B' => 30, 'C' => 14, 'D' => 16];
    }
}
