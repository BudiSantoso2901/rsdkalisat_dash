<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::guard('dashboard')->check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi menggunakan akun SIMRS.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $username = trim($validated['username']);

        // Pembatasan percobaan login berdasarkan username dan IP.
        $throttleKey = 'dashboard-login:' . sha1(
            Str::lower($username) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
                ])
                ->onlyInput('username');
        }

        // Autentikasi menggunakan tabel users SIMRS.
        // Hanya akun dengan status 1 yang diperbolehkan.
        $credentials = [
            'username' => $username,
            'password' => $validated['password'],
            'status' => 1,
        ];

        // Remember Me sengaja dinonaktifkan agar
        // remember_token SIMRS tidak mengalami perubahan.
        if (!Auth::guard('dashboard')->attempt($credentials, false)) {

            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors([
                    'username' => 'Username atau password salah, atau akun tidak aktif.'
                ])
                ->onlyInput('username');
        }

        // Login berhasil.
        RateLimiter::clear($throttleKey);

        // Regenerasi session untuk keamanan.
        $request->session()->regenerate();

        // Tidak melakukan UPDATE pada tabel users SIMRS.
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout akun Dashboard.
     */
    public function logout(Request $request)
    {
        Auth::guard('dashboard')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Anda berhasil keluar dari aplikasi.');
    }
}