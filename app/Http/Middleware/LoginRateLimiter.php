<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function __construct(private readonly RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->routeIs('login')) {
            $key = $this->throttleKey($request);

            // Maksimal 5 percobaan per 60 detik per IP+email
            if ($this->limiter->tooManyAttempts($key, 5)) {
                $seconds = $this->limiter->availableIn($key);

                Log::warning('Web login rate limit exceeded', [
                    'ip'    => $request->ip(),
                    'email' => $request->input('email'),
                ]);

                return back()
                    ->withErrors([
                        'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
                    ])
                    ->withInput($request->only('email'));
            }

            $response = $next($request);

            // Jika login gagal (redirect back dengan errors), tambah counter
            if ($response->isRedirect() && session()->has('errors')) {
                $this->limiter->hit($key, 60);
            } else {
                // Login berhasil, reset counter
                $this->limiter->clear($key);
            }

            return $response;
        }

        return $next($request);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('email', '')) . '|' . $request->ip()
        );
    }

}
