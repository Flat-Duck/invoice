<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SetApplicationLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Schema::hasTable('settings')) {
            app()->setLocale(Setting::valueFor('language', 'en'));
        }

        return $next($request);
    }
}
