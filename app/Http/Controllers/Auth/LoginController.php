<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function store(LoginRequest $request, AuthenticatedSessionController $controller): RedirectResponse
    {
        return $controller->store($request);
    }

    /**
     * Log the user out.
     */
    public function destroy(Request $request, AuthenticatedSessionController $controller): RedirectResponse
    {
        return $controller->destroy($request);
    }
}
