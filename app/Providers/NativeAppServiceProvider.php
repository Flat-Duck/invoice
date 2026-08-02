<?php

namespace App\Providers;

use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    public function boot(): void
    {
        Window::open('main')
            ->title('InvoicePro')
            ->url(url('/'))
            ->width(1440)
            ->height(900)
            ->minWidth(390)
            ->minHeight(700);
    }

    public function phpIni(): array
    {
        return [
            'memory_limit' => '256M',
            'max_execution_time' => '120',
        ];
    }
}
