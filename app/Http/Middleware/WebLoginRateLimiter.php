<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WebLoginRateLimiter
{
    public function __construct(private readonly RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Hanya berlaku untuk POST /login
        if (! ($request->isMethod('POST') && $request->routeIs('login'))) {
            return $next($request);
        }

        $key = 'web-login:' . Str::lower($request->input('email', '')) . '|' . $request->ip();

        if ($this->limiter->tooManyAttempts($key, 5)) {
            $seconds = $this->limiter->availableIn($key);

            return back()
                ->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."])
                ->withInput($request->only('email'));
        }

        $response = $next($request);

        // Jika redirect ke login lagi (gagal), tambah counter
        if ($response->isRedirect(route('login')) || session()->has('errors')) {
            $this->limiter->hit($key, 60);
        } else {
            $this->limiter->clear($key);
        }

        return $response;
    }
}
