<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureInvoiceUserAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((bool) $request->session()->get('invoice_super_admin', false)) {
            return $next($request);
        }

        if (Auth::check() && Auth::user()->active) {
            return $next($request);
        }

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login');
    }
}
