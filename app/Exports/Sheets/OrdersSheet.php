<?php

namespace App\Exports\Sheets;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrdersSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private int $days) {}

    public function title(): string { return 'Orders'; }

    public function headings(): array
    {
        return ['Order #', 'Date & Time', 'Items', 'Subtotal (₱)', 'Tax (₱)', 'Total (₱)', 'Payment', 'Received (₱)', 'Change (₱)'];
    }

    public function collection()
    {
        $from = now()->subDays($this->days - 1)->startOfDay();

        return Order::with('items')
            ->where('created_at', '>=', $from)
            ->orderByDesc('id')
            ->get()
            ->map(fn($o) => [
                '#' . $o->order_number,
                $o->created_at->format('Y-m-d H:i'),
                $o->items->map(fn($i) => $i->product_name . ' ×' . $i->quantity)->implode(', '),
                number_format($o->subtotal, 2),
                number_format($o->tax, 2),
                number_format($o->total, 2),
                $o->payment_method,
                number_format($o->amount_received, 2),
                number_format($o->change_given, 2),
            ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4a2c1a']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 18, 'C' => 45, 'D' => 14, 'E' => 12, 'F' => 14, 'G' => 12, 'H' => 14, 'I' => 12];
    }
}
