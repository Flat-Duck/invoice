<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invoice;
use App\Services\ReportQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_date_populates_period(): void
    {
        $invoice = Invoice::factory()->create([
            'invoice_number' => 'INV-2026-001',
            'invoice_date' => '2026-07-14',
            'amount' => 100,
            'exchange_rate' => 4.85,
            'work_order' => 'WO-42',
            'service_agreement' => 'SA-10',
            'to_reference' => 'TO-7',
        ]);
        $this->assertSame(2026, $invoice->invoice_year);
        $this->assertSame(7, $invoice->invoice_month);
        $this->assertSame(485.0, $invoice->total_lyd);
        $this->assertSame('WO: WO-42 / SA: SA-10 / TO: TO-7', $invoice->agreement_references);
    }

    public function test_report_query_filters_and_eager_loads(): void
    {
        $company = Company::factory()->create(['name' => 'Atlas Trading']);
        Invoice::factory()->for($company)->create(['invoice_number' => 'INV-2026-777', 'invoice_date' => '2026-07-10', 'location' => 'Tripoli']);
        Invoice::factory()->for($company)->create(['invoice_date' => '2026-06-10', 'location' => 'Benghazi']);

        $results = app(ReportQuery::class)->build(['year' => 2026, 'month' => 7, 'company_id' => $company->id, 'location' => 'Tripoli', 'search' => 'Atlas', 'sort' => 'date'])->get();

        $this->assertCount(1, $results);
        $this->assertSame('INV-2026-777', $results->first()->invoice_number);
        $this->assertTrue($results->first()->relationLoaded('company'));
    }

    public function test_report_exports_are_available(): void
    {
        Invoice::factory()->create(['invoice_date' => '2026-07-10']);
        $session = ['invoice_super_admin' => true, 'invoice_user_name' => 'Super Admin'];
        $this->withSession($session)->get('/reports/export/csv?year=2026&month=7')->assertOk();
        $this->withSession($session)->get('/reports/export/pdf?year=2026&month=7')->assertOk();
    }

    public function test_filtered_report_has_a_print_preview(): void
    {
        Invoice::factory()->create([
            'invoice_date' => '2026-07-10',
            'amount' => 100,
            'exchange_rate' => 4.85,
            'notes' => 'Fuel services',
        ]);

        $this->withSession(['invoice_super_admin' => true])
            ->get('/reports/print?type=monthly&year=2026&month=7')
            ->assertOk()
            ->assertSee('Monthly Invoice Report')
            ->assertSee('485.00')
            ->assertSee('Fuel services')
            ->assertSee('Print report');
    }
}
