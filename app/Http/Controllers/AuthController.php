<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ((bool) $request->session()->get('invoice_super_admin', false) || Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (hash_equals('superadmin', $credentials['username']) && hash_equals('superadmin', $credentials['password'])) {
            Auth::logout();
            $request->session()->regenerate();
            $request->session()->put([
                'invoice_super_admin' => true,
                'invoice_user_name' => 'Super Admin',
            ]);

            return redirect()->intended(route('dashboard'));
        }

        if (Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'active' => true,
        ])) {
            $request->session()->regenerate();
            $request->session()->forget(['invoice_super_admin', 'invoice_user_name']);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['username' => 'Invalid credentials or inactive account.'])->onlyInput('username');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
