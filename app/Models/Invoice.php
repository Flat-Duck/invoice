<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    protected $fillable = ['company_id', 'administration_id', 'invoice_number', 'work_order', 'service_agreement', 'to_reference', 'invoice_date', 'received_date', 'financial_return_date', 'invoice_year', 'invoice_month', 'amount', 'exchange_rate', 'location', 'notes'];

    protected function casts(): array
    {
        return ['invoice_date' => 'date', 'received_date' => 'date', 'financial_return_date' => 'date', 'amount' => 'decimal:2', 'exchange_rate' => 'decimal:6'];
    }

    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice): void {
            if ($invoice->invoice_date) {
                $invoice->invoice_year = (int) $invoice->invoice_date->format('Y');
                $invoice->invoice_month = (int) $invoice->invoice_date->format('n');
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function administration(): BelongsTo
    {
        return $this->belongsTo(Administration::class);
    }

    public function getTotalLydAttribute(): float
    {
        return round((float) $this->amount * (float) $this->exchange_rate, 2);
    }

    public function getAgreementReferencesAttribute(): string
    {
        return collect([
            $this->work_order ? 'WO: '.$this->work_order : null,
            $this->service_agreement ? 'SA: '.$this->service_agreement : null,
            $this->to_reference ? 'TO: '.$this->to_reference : null,
        ])->filter()->join(' / ') ?: '—';
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, fn (Builder $q, string $term) => $q->where(
            fn (Builder $q) => $q->where('invoice_number', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhereHas('administration', fn (Builder $q) => $q->where('name', 'like', "%{$term}%"))
                ->orWhereHas('company', fn (Builder $q) => $q->where('name', 'like', "%{$term}%"))
        ));
    }
}
