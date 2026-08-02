<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $reportTitle }} · InvoicePro</title>
    @vite(['resources/css/app.css'])
</head>
<body class="print-preview-body">
@php
    $filterItems = collect([
        'Year' => $filters['year'] ?? null,
        'Month' => isset($filters['month']) && $filters['month'] ? DateTime::createFromFormat('!m', $filters['month'])->format('F') : null,
        'Company' => isset($filters['company_id']) && $filters['company_id'] ? App\Models\Company::find($filters['company_id'])?->name : null,
        'Location' => $filters['location'] ?? null,
        'Search' => $filters['search'] ?? null,
    ])->filter(fn ($value) => filled($value));
@endphp
<div class="print-toolbar">
    <a class="btn" href="{{ route('reports', array_filter($filters)) }}">← Back to reports</a>
    <div>
        <a class="btn" href="{{ route('reports.export', array_merge(['format' => 'pdf'], $filters)) }}">Export PDF</a>
        <button class="btn primary" onclick="window.print()">Print report</button>
    </div>
</div>
<main class="print-sheet">
    <header class="print-header">
        <div><div class="print-logo">IP</div><div><strong>InvoicePro</strong><small>Offline invoice reporting</small></div></div>
        <div class="print-date"><span>Generated</span><strong>{{ now()->format('F j, Y') }}</strong><small>{{ now()->format('g:i A') }}</small></div>
    </header>
    <section class="print-title">
        <h1>{{ $reportTitle }}</h1>
        @if($filterItems->isNotEmpty())<div class="print-filters">@foreach($filterItems as $label=>$value)<span><b>{{ $label }}:</b> {{ $value }}</span>@endforeach</div>@else<p>All invoice records</p>@endif
    </section>
    <table class="print-table">
        <thead><tr><th>Month</th><th>Company</th><th>Location</th><th>WO / SA &amp; TO</th><th>Expenses</th><th>Ex Rate</th><th>Total LYD</th><th>Remarks</th></tr></thead>
        <tbody>
        @forelse($invoices as $invoice)
            <tr><td>{{ $invoice->invoice_date->format('F Y') }}</td><td>{{ $invoice->company->name }}</td><td>{{ $invoice->location }}</td><td>{{ $invoice->agreement_references }}</td><td>{{ number_format($invoice->amount,2) }}</td><td>{{ number_format($invoice->exchange_rate,6) }}</td><td><strong>{{ number_format($invoice->total_lyd,2) }}</strong></td><td>{{ $invoice->notes ?: '—' }}</td></tr>
        @empty
            <tr><td colspan="8" class="print-empty">No invoices match these filters.</td></tr>
        @endforelse
        </tbody>
    </table>
    <section class="print-summary">
        <div><span>Total invoices</span><strong>{{ number_format($invoices->count()) }}</strong></div>
        <div><span>Total expenses</span><strong>{{ number_format($invoices->sum('amount'),2) }}</strong></div>
        <div><span>Total LYD</span><strong>{{ number_format($invoices->sum(fn($invoice) => $invoice->total_lyd),2) }} LYD</strong></div>
    </section>
    <footer class="print-footer"><span>InvoicePro · Confidential business report</span><span>Generated locally</span></footer>
</main>
</body>
</html>
