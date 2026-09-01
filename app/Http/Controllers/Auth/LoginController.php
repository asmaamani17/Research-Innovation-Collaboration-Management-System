<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'staff_id' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to find user by IC number
        $user = User::where('ic', $credentials['staff_id'])->first();

        if (!$user || !\Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'staff_id' => 'Nama pengguna atau kata laluan tidak sah.',
            ])->withInput($request->except('password'));
        }

        // Log the user in
        Auth::login($user);

        // Regenerate session
        $request->session()->regenerate();

        // Redirect to admin dashboard
        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Log the user out.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
