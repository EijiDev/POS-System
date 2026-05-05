<?php

namespace App\Exports\Sheets;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExpensesSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private int $days) {}

    public function title(): string { return 'Expenses'; }

    public function headings(): array
    {
        return ['Date', 'Category', 'Description', 'Amount (₱)', 'Status'];
    }

    public function collection()
    {
        $from = now()->subDays($this->days - 1)->startOfDay();

        return Expense::where('date', '>=', $from->toDateString())
            ->orderByDesc('date')
            ->get()
            ->map(fn($e) => [
                $e->date->format('Y-m-d'),
                $e->category,
                $e->description,
                number_format($e->amount, 2),
                $e->status,
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
        return ['A' => 14, 'B' => 16, 'C' => 30, 'D' => 16, 'E' => 12];
    }
}
