<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersAndProtection
{
    public function __construct(private readonly RateLimiter $limiter) {}

    public function handle(Request $request, Closure $next): Response
    {
        // ── 1. DDoS / Rate limit global ───────────────────────────────────────
        $ipKey = 'global:' . $request->ip();

        if ($this->limiter->tooManyAttempts($ipKey, 120)) {
            $seconds = $this->limiter->availableIn($ipKey);

            Log::warning('Global rate limit exceeded', [
                'ip'  => $request->ip(),
                'url' => $request->fullUrl(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => "Terlalu banyak request. Coba lagi dalam {$seconds} detik.",
            ], 429)->withHeaders([
                'Retry-After'       => $seconds,
                'X-RateLimit-Limit' => 120,
            ]);
        }

        $this->limiter->hit($ipKey, 60);

        // ── 2. SQL Injection protection ───────────────────────────────────────
        $queryString = urldecode($request->getQueryString() ?? '');
        $sqlPatterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b)/i',
            '/(\bdrop\b.*\btable\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            '/(\bdelete\b.*\bfrom\b)/i',
            '/(\bexec\b|\bexecute\b|\bxp_\w+)/i',
            '/(--|\bor\b\s+\d+=\d+|\bor\b\s+\'[^\']+\'=\'[^\']+\')/i',
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, $queryString)) {
                Log::warning('SQL injection pattern detected', [
                    'ip'    => $request->ip(),
                    'url'   => $request->fullUrl(),
                    'query' => $queryString,
                ]);

                abort(400, 'Request tidak valid.');
            }
        }

        // ── 3. Proses request ─────────────────────────────────────────────────
        $response = $next($request);

        // ── 4. Security Headers ───────────────────────────────────────────────
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // ── 5. CSP — berbeda antara local dev dan production ──────────────────
        $response->headers->set(
            'Content-Security-Policy',
            $this->buildCsp(),
        );

        // HSTS hanya aktif di production (HTTPS)
        if (app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }

private function buildCsp(): ?string
{
    if (app()->isLocal()) {
        return null;
    }

    return implode('; ', [
        // "default-src 'self'",
        // "script-src 'self' 'unsafe-inline'",
        // "style-src 'self' 'unsafe-inline'",
        "img-src 'self' data: blob:",
        // "font-src 'self' data:",
        // "connect-src 'self'",
        "frame-ancestors 'none'",
        "base-uri 'self'",
        "form-action 'self'",
        "upgrade-insecure-requests",
    ]);
}


}
