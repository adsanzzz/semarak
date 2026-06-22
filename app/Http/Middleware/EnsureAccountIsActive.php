<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || $user->role !== 2 || $user->is_active) {
            return $next($request);
        }

        $allowedRoutes = [
            'pengaduan',
            'pengaduan.store',
            'logout',
        ];

        if (in_array($request->route()?->getName(), $allowedRoutes, true)) {
            return $next($request);
        }

        return redirect()->route('pengaduan')->with('error', 'Akun toko Anda telah dinonaktifkan. Silakan ajukan banding melalui menu Pengaduan & Komplain.');
    }
}