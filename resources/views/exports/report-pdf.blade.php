<!doctype html>
<html><head><meta charset="utf-8"><style>
body{font-family:DejaVu Sans,sans-serif;color:#14213d;font-size:11px}h1{font-size:22px;margin:0 0 4px}.muted{color:#667085}table{width:100%;border-collapse:collapse;margin-top:20px}th{background:#eef4ff;text-align:left}th,td{padding:8px;border-bottom:1px solid #dce3ed}.totals{margin-top:18px;text-align:right;font-weight:bold}.footer{position:fixed;bottom:0;color:#667085;font-size:9px}
</style></head><body>
<h1>Invoice Report</h1>
<div class="muted">Generated {{ now()->format('F j, Y g:i A') }} · Filters: {{ collect($filters)->filter()->map(fn($v,$k)=>ucwords(str_replace('_',' ',$k)).': '.$v)->join(' · ') ?: 'All invoices' }}</div>
<table><thead><tr><th>Month</th><th>Company</th><th>Location</th><th>WO / SA &amp; TO</th><th>Expenses</th><th>Ex Rate</th><th>Total LYD</th><th>Remarks</th></tr></thead>
<tbody>@foreach($invoices as $invoice)<tr><td>{{ $invoice->invoice_date->format('F Y') }}</td><td>{{ $invoice->company->name }}</td><td>{{ $invoice->location }}</td><td>{{ $invoice->agreement_references }}</td><td>{{ number_format($invoice->amount,2) }}</td><td>{{ number_format($invoice->exchange_rate,6) }}</td><td>{{ number_format($invoice->total_lyd,2) }}</td><td>{{ $invoice->notes ?: '—' }}</td></tr>@endforeach</tbody></table>
<div class="totals">Total invoices: {{ $invoices->count() }} &nbsp; Total expenses: {{ number_format($invoices->sum('amount'),2) }} &nbsp; Total LYD: {{ number_format($invoices->sum(fn($invoice) => $invoice->total_lyd),2) }} LYD</div>
<div class="footer">InvoicePro · Offline invoice reporting</div>
</body></html>
