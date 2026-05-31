<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
            }

            abort(403, 'Akses hanya untuk admin.');
        }

        return $next($request);
    }
}
