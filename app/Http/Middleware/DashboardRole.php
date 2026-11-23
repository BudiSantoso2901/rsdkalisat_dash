<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('dashboard')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Periksa role utama pengguna.
        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
        }

        return $next($request);
    }
}
