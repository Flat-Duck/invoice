<?php

namespace App\Exports;

use App\Services\ReportQuery;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters) {}

    public function query()
    {
        return app(ReportQuery::class)->build($this->filters);
    }

    public function headings(): array
    {
        return array_map(fn (string $heading) => __($heading), ['Month', 'Company', 'Administration', 'Location', 'WO / SA & TO', 'Expenses', 'Ex Rate', 'Total LYD', 'Received Date', 'Returned to Financial', 'Remarks']);
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_date->format('F Y'),
            $invoice->company->name,
            $invoice->administration?->name,
            $invoice->location,
            $invoice->agreement_references,
            $invoice->amount,
            $invoice->exchange_rate,
            $invoice->total_lyd,
            $invoice->received_date?->format('Y-m-d'),
            $invoice->financial_return_date?->format('Y-m-d'),
            $invoice->notes,
        ];
    }
}
