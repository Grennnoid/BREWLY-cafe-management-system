<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Staff;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun staf dinonaktifkan. Hubungi Administrator.']);
            }

            $request->session()->regenerate();
            return $this->redirectBasedOnRole($user);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function quickLogin($role)
    {
        $role = strtoupper($role);
        $staff = Staff::where('role', $role)->where('is_active', true)->first();

        if ($staff) {
            Auth::login($staff);
            request()->session()->regenerate();
            return $this->redirectBasedOnRole($staff);
        }

        return redirect()->route('login')->with('error', "Staf dengan role {$role} tidak ditemukan.");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    protected function redirectBasedOnRole(Staff $user)
    {
        return match ($user->role) {
            'ADMIN' => redirect()->route('admin.dashboard'),
            'CASHIER' => redirect()->route('cashier.orders'),
            'KITCHEN' => redirect()->route('kitchen.index'),
            default => redirect()->route('home'),
        };
    }
}

