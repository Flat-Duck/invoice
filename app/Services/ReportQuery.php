<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

final class ReportQuery
{
    public function build(array $filters): Builder
    {
        $sort = $filters['sort'] ?? 'date';
        $columns = ['date' => 'invoice_date', 'amount' => 'amount', 'location' => 'location', 'company' => 'companies.name'];
        $query = Invoice::query()->with('company');

        if ($sort === 'company') {
            $query->join('companies', 'companies.id', '=', 'invoices.company_id')->select('invoices.*');
        }

        return $query->search($filters['search'] ?? null)
            ->when($filters['year'] ?? null, fn (Builder $q, $value) => $q->where('invoice_year', $value))
            ->when($filters['month'] ?? null, fn (Builder $q, $value) => $q->where('invoice_month', $value))
            ->when($filters['company_id'] ?? null, fn (Builder $q, $value) => $q->where('company_id', $value))
            ->when($filters['location'] ?? null, fn (Builder $q, $value) => $q->where('location', $value))
            ->orderBy($columns[$sort] ?? 'invoice_date', $filters['direction'] ?? 'desc');
    }
}
