<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesReportExport;
use App\Services\ReportQuery;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportExportController extends Controller
{
    public function __invoke(Request $request, string $format)
    {
        abort_unless(in_array($format, ['pdf', 'xlsx', 'csv']), 404);
        $filters = $this->filters($request);
        $name = 'invoice-report-'.now()->format('Y-m-d-His');

        if ($format === 'pdf') {
            $invoices = app(ReportQuery::class)->build($filters)->get();
            return Pdf::loadView('exports.report-pdf', compact('invoices', 'filters'))
                ->setPaper('a4', 'landscape')->download($name.'.pdf');
        }

        return Excel::download(new InvoicesReportExport($filters), $name.'.'.$format);
    }

    public function preview(Request $request): View
    {
        $filters = $this->filters($request);
        $invoices = app(ReportQuery::class)->build($filters)->get();
        $type = in_array($filters['type'] ?? null, ['monthly', 'yearly', 'company', 'location'])
            ? $filters['type']
            : 'monthly';

        return view('reports.print', [
            'invoices' => $invoices,
            'filters' => $filters,
            'reportTitle' => ucfirst($type).' Invoice Report',
        ]);
    }

    private function filters(Request $request): array
    {
        return $request->only(['type', 'year', 'month', 'company_id', 'location', 'search', 'sort', 'direction']);
    }
}
