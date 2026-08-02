<?php

namespace App\Livewire\Reports;

use App\Models\Company;
use App\Models\Invoice;
use App\Services\ReportQuery;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url] public string $type = 'monthly';
    #[Url] public ?int $year = null;
    #[Url] public ?int $month = null;
    #[Url] public ?int $companyId = null;
    #[Url] public string $location = '';
    #[Url] public string $search = '';
    #[Url] public string $sort = 'date';
    public string $direction = 'desc';

    public function mount(): void
    {
        $this->year ??= now()->year;
        $this->month ??= now()->month;
    }

    public function updated($property): void
    {
        if (in_array($property, ['type', 'year', 'month', 'companyId', 'location', 'search', 'sort'])) {
            $this->resetPage();
        }
        if ($property === 'type') {
            if ($this->type === 'yearly') $this->month = null;
            if ($this->type !== 'company') $this->companyId = null;
            if ($this->type !== 'location') $this->location = '';
        }
    }

    public function filters(): array
    {
        return [
            'type' => $this->type, 'year' => $this->year, 'month' => $this->month, 'company_id' => $this->companyId,
            'location' => $this->location ?: null, 'search' => $this->search,
            'sort' => $this->sort, 'direction' => $this->direction,
        ];
    }

    public function render()
    {
        $query = app(ReportQuery::class)->build($this->filters());
        $totals = (clone $query)->reorder()->selectRaw(
            'COUNT(*) as count, COALESCE(SUM(amount), 0) as expenses, COALESCE(SUM(amount * exchange_rate), 0) as total_lyd'
        )->first();

        return view('livewire.reports.index', [
            'invoices' => $query->paginate(10),
            'totals' => $totals,
            'companies' => Company::orderBy('name')->get(),
            'locations' => Invoice::query()->distinct()->orderBy('location')->pluck('location'),
        ])->layout('components.layouts.app', ['title' => 'Reports']);
    }
}
