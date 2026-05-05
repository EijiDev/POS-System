<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrdersExport implements WithMultipleSheets
{
    public function __construct(private int $days) {}

    public function sheets(): array
    {
        return [
            new Sheets\SummarySheet($this->days),
            new Sheets\OrdersSheet($this->days),
            new Sheets\PopularItemsSheet($this->days),
            new Sheets\CategorySheet($this->days),
            new Sheets\ExpensesSheet($this->days),
        ];
    }
}
