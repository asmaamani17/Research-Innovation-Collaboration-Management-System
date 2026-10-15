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

        // Superadmin should go to superadmin dashboard
        if ($request->user()->hasRole('superadmin') || $request->user()->hasRole('super_admin')) {
            session(['workspace' => 'superadmin']);
            return redirect()->intended(route('superadmin.dashboard'));
        }

        if ($request->user()->hasRole(['admin', 'staff'])) {
            $moduleLandingRoutes = [
                'KPI' => 'admin.kpi.index',
                'Awards' => 'admin.awards.dashboard',
                'Products' => 'admin.products.index',
                'Intellectual Property' => 'admin.ip.dashboard',
                'Community/STEM/Industry' => 'admin.stem.dashboard',
            ];

            foreach ($moduleLandingRoutes as $module => $route) {
                if ($request->user()->hasModule($module)) {
                    return redirect()->route($route);
                }
            }
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
