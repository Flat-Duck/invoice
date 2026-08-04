<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $reportTitle }} · InvoicePro</title>
    @vite(['resources/css/app.css'])
</head>
<body class="print-preview-body expense-report-body">
@php
    $arabicMonths = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
    $month = (int) ($filters['month'] ?? 0);
    $year = $filters['year'] ?? null;
    $period = $month ? 'لشهر '.$arabicMonths[$month].($year ? ' '.$year : '') : ($year ? 'لسنة '.$year : 'لجميع الفترات');
    $displayTitle = app()->isLocale('ar') ? 'تقرير مصروفات الإدارات '.$period : 'Administration Expense Report'.($month ? ' for '.DateTime::createFromFormat('!m', $month)->format('F').($year ? ' '.$year : '') : ($year ? ' for '.$year : ' - All Periods'));
    $logoPath = public_path('images/report-logo.png');
    $logoData = file_exists($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
@endphp
<div class="print-toolbar" dir="ltr">
    <a class="btn" href="{{ route('reports', array_filter($filters)) }}">← {{ __('Back to reports') }}</a>
    <div>
        <a class="btn" href="{{ route('reports.export', array_merge(['format' => 'pdf'], $filters)) }}">{{ __('Export PDF') }}</a>
        <button class="btn primary" onclick="window.print()">{{ __('Print report') }}</button>
    </div>
</div>
<main class="print-sheet expense-report-sheet">
    <header class="expense-report-header">
        <div class="report-date" dir="ltr"><span>{{ __('Report date') }}</span><strong>{{ now()->format('Y/m/d') }}</strong></div>
        <h1>{{ $displayTitle }}</h1>
        <div class="report-logo-slot">
            @if($logoData)<img src="{{ $logoData }}" alt="Company logo">@endif
        </div>
    </header>

    <div class="expense-report-table-wrap">
        <table class="expense-report-table">
            <thead><tr><th>#</th><th>{{ __('Month') }}</th><th>{{ __('Company') }}</th><th>{{ __('Business Field') }}</th><th>{{ __('Administration') }}</th><th>{{ __('Location') }}</th><th>WO / SA / TO</th><th>{{ __('Expenses') }}</th><th>{{ __('Exchange rate') }}</th><th>{{ __('Total LYD') }}</th><th>{{ __('Received') }}</th><th>{{ __('Returned to Financial') }}</th><th>{{ __('Remarks') }}</th></tr></thead>
            <tbody>
            @forelse($invoices as $invoice)
                <tr><td>{{ $loop->iteration }}</td><td>{{ app()->isLocale('ar') ? $arabicMonths[$invoice->invoice_month] : $invoice->invoice_date->format('F') }} {{ $invoice->invoice_year }}</td><td>{{ $invoice->company->name }}</td><td>{{ $invoice->company->business_field ?: '—' }}</td><td>{{ $invoice->administration?->name ?? '—' }}</td><td>{{ $invoice->location }}</td><td dir="ltr">{{ $invoice->agreement_references }}</td><td dir="ltr">{{ number_format($invoice->amount,2) }}</td><td dir="ltr">{{ number_format($invoice->exchange_rate,6) }}</td><td dir="ltr"><strong>{{ number_format($invoice->total_lyd,2) }}</strong></td><td dir="ltr">{{ $invoice->received_date?->format('Y/m/d') ?? '—' }}</td><td dir="ltr">{{ $invoice->financial_return_date?->format('Y/m/d') ?? '—' }}</td><td>{{ $invoice->notes ?: '—' }}</td></tr>
            @empty
                <tr><td colspan="13" class="print-empty">{{ __('No invoices match these filters.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <section class="expense-report-summary">
        <div><span>{{ __('Total invoices') }}</span><strong>{{ number_format($invoices->count()) }}</strong></div>
        <div><span>{{ __('Total expenses') }}</span><strong>{{ number_format($invoices->sum('amount'),2) }}</strong></div>
        <div><span>{{ __('Total LYD') }}</span><strong>{{ number_format($invoices->sum(fn($invoice) => $invoice->total_lyd),2) }} {{ __('LYD') }}</strong></div>
    </section>
    <footer class="expense-report-footer"><span>InvoicePro</span><span>{{ __('Confidential internal report') }}</span></footer>
</main>
</body>
</html>
