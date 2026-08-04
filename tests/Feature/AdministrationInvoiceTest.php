<?php

namespace Tests\Feature;

use App\Models\Administration;
use App\Models\Invoice;
use App\Models\Setting;
use App\Livewire\Invoices\Index as InvoiceIndex;
use App\Livewire\Settings\Index as SettingsIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdministrationInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_belongs_to_an_administration_and_casts_workflow_dates(): void
    {
        $administration = Administration::create(['name' => 'Operations']);
        $invoice = Invoice::factory()->create([
            'administration_id' => $administration->id,
            'received_date' => '2026-08-01',
            'financial_return_date' => '2026-08-03',
        ]);

        $this->assertTrue($invoice->administration->is($administration));
        $this->assertSame('2026-08-01', $invoice->received_date->toDateString());
        $this->assertSame('2026-08-03', $invoice->financial_return_date->toDateString());
    }

    public function test_administration_and_invoice_screens_are_available(): void
    {
        $session = ['invoice_super_admin' => true, 'invoice_user_name' => 'Super Admin'];

        $this->withSession($session)->get('/administrations')->assertOk()->assertSee('Administrations');
        $this->withSession($session)->get('/invoices')->assertOk()->assertSee('Returned to Financial');
    }

    public function test_invoice_month_dropdown_updates_the_invoice_date_safely(): void
    {
        Livewire::test(InvoiceIndex::class)
            ->set('invoice_date', '2026-01-31')
            ->set('invoice_month', '2')
            ->assertSet('invoice_date', '2026-02-28');
    }

    public function test_saved_arabic_language_is_applied_with_rtl_layout(): void
    {
        Setting::create(['key' => 'language', 'value' => 'ar']);

        $this->withSession(['invoice_super_admin' => true])
            ->get('/settings')
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('الإعدادات');
    }

    public function test_language_can_be_changed_from_settings(): void
    {
        Livewire::test(SettingsIndex::class)
            ->set('language', 'ar')
            ->call('save')
            ->assertRedirect(route('settings'));

        $this->assertSame('ar', Setting::valueFor('language'));
    }
}
