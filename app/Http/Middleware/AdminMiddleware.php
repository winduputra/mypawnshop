<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'owner', 'superadmin'])) {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Hanya admin/owner yang dapat mengakses halaman ini.');
    }
}
