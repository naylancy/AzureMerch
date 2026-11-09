<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UKasir
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (auth()->user()->role !== 'kasir') {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengakses Modul Kasir POS.');
        }

        return $next($request);
    }
}
