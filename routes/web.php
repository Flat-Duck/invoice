<?php

use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureInvoiceUserAuthenticated;
use App\Livewire\Companies\Index as Companies;
use App\Livewire\Dashboard;
use App\Livewire\Invoices\Index as Invoices;
use App\Livewire\Reports\Index as Reports;
use App\Livewire\Settings\Index as Settings;
use App\Livewire\Users\Index as Users;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');

Route::middleware(EnsureInvoiceUserAuthenticated::class)->group(function (): void {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/companies', Companies::class)->name('companies');
    Route::get('/invoices', Invoices::class)->name('invoices');
    Route::get('/reports', Reports::class)->name('reports');
    Route::get('/users', Users::class)->name('users');
    Route::get('/settings', Settings::class)->name('settings');
    Route::get('/reports/print', [ReportExportController::class, 'preview'])->name('reports.print');
    Route::get('/reports/export/{format}', ReportExportController::class)->name('reports.export');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});
