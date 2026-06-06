<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cegah clickjacking — halaman tidak boleh di-embed di iframe
        $response->headers->set('X-Frame-Options', 'DENY');

        // Cegah MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Aktifkan XSS filter di browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Paksa HTTPS selama 1 tahun termasuk subdomain (aktif saat HTTPS)
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        // Batasi informasi referrer yang dikirim ke situs lain
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Batasi fitur browser yang bisa dipakai (cegah abuse geolocation, camera, dll)
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=()'
        );

        // Hapus header yang membocorkan teknologi server
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Content Security Policy — cegah XSS dan injeksi resource asing
        // 'nonce' bisa ditambahkan nanti untuk inline scripts jika dibutuhkan
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'",   // unsafe-inline diperlukan untuk Blade inline JS
            "style-src 'self' 'unsafe-inline'",    // diperlukan untuk Tailwind inline
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "frame-ancestors 'none'",              // double protection clickjacking
            "base-uri 'self'",
            "form-action 'self'",
            "upgrade-insecure-requests",           // otomatis upgrade HTTP ke HTTPS
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // CORS — hanya izinkan origin yang terdaftar
        $allowedOrigin = config('app.url');
        if ($request->headers->get('Origin') === $allowedOrigin) {
            $response->headers->set('Access-Control-Allow-Origin', $allowedOrigin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }

        return $response;
    }
}
