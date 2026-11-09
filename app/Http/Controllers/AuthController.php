<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Support login by username or email
        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $attempt = [
            $fieldType => $credentials['login'],
            'password' => $credentials['password'],
        ];

        if (Auth::attempt($attempt, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Akun Anda dinonaktifkan oleh Administrator.',
                ])->onlyInput('login');
            }

            $request->session()->regenerate();

            return $this->redirectByRole($user)
                ->with('success', 'Selamat datang kembali, ' . $user->name . ' (' . ucfirst($user->role) . ')!');
        }

        return back()->withErrors([
            'login' => 'Kombinasi kredensial login dan password tidak valid.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil logout dari sistem AzureMerch.');
    }

    protected function redirectByRole(User $user)
    {
        return match($user->role) {
            'kasir' => redirect()->route('pos.index'),
            'gudang' => redirect()->route('warehouse.index'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
