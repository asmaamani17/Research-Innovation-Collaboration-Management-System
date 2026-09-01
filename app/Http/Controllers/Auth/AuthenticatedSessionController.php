<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // KPI admin should go to KPI index
        if ($request->user()->ic === '883456789013') {
            return redirect()->intended(route('admin.kpi.index'));
        }

        // Superadmin should go to superadmin dashboard
        if ($request->user()->hasRole('superadmin') || $request->user()->hasRole('super_admin')) {
            session(['workspace' => 'superadmin']);
            return redirect()->intended(route('superadmin.dashboard'));
        }

        if ($request->user()->hasRole('admin')) {
            session(['workspace' => 'awards']);
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
