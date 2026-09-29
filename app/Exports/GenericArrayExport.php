<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * One reusable Excel export for every report in the system (Sales, Purchases,
 * Sales Returns, Purchase Returns, Expenses, Profit & Loss, Inventory).
 * Each report controller just supplies its own column headings + row data.
 */
class GenericArrayExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected array $headers,
        protected array $rows,
        protected string $title = "Report"
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headers;
    }

    public function title(): string
    {
        return $this->title;
    }
}
