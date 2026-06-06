<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PreventPhising
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
     // IP yang diblokir permanen (isi setelah dapat laporan abuse)
    private array $blockedIps = [];

    // User-agent yang dikenal sebagai scraper/scanner berbahaya
    private array $suspiciousAgents = [
        'sqlmap', 'nikto', 'nmap', 'masscan', 'zgrab',
        'nessus', 'openvas', 'w3af', 'havij', 'acunetix',
        'burpsuite', 'dirbuster', 'gobuster', 'wfuzz',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ip        = $request->ip();
        $userAgent = strtolower($request->userAgent() ?? '');

        // 1. Blokir IP yang sudah di-blacklist
        if (in_array($ip, $this->blockedIps, true)) {
            Log::warning('Blocked IP attempted access', [
                'ip'         => $ip,
                'url'        => $request->fullUrl(),
                'user_agent' => $request->userAgent(),
            ]);

            abort(403, 'Akses ditolak.');
        }

        // 2. Blokir scanner dan exploit tools
        foreach ($this->suspiciousAgents as $agent) {
            if (str_contains($userAgent, $agent)) {
                Log::warning('Suspicious scanner detected', [
                    'ip'         => $ip,
                    'user_agent' => $request->userAgent(),
                    'matched'    => $agent,
                ]);

                abort(403, 'Akses ditolak.');
            }
        }

        // 3. Cegah host header injection (phishing via manipulasi Host header)
        $host        = $request->getHost();
        $appUrl      = parse_url(config('app.url'), PHP_URL_HOST);
        $allowedHosts = array_filter([
            $appUrl,
            'localhost',
            '127.0.0.1',
            env('ADDITIONAL_ALLOWED_HOST'), // untuk staging/preview
        ]);

        if ($appUrl && ! in_array($host, $allowedHosts, true)) {
            Log::warning('Host header injection attempt', [
                'host'    => $host,
                'ip'      => $ip,
                'referer' => $request->header('Referer'),
            ]);

            abort(400, 'Host tidak valid.');
        }

        // 4. Cegah path traversal
        $path = $request->path();
        if (preg_match('/(\.\.[\/\\\\]|\.\.%2[Ff]|\.\.%5[Cc])/', $path)) {
            Log::warning('Path traversal attempt', ['ip' => $ip, 'path' => $path]);
            abort(400, 'Request tidak valid.');
        }

        // 5. Deteksi SQL injection pattern di query string
        $queryString = $request->getQueryString() ?? '';
        $sqlPatterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b)/i',
            '/(\bdrop\b.*\btable\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            "/(--|;|'|\"|\/\*|\*\/)/",
            '/(\bexec\b|\bexecute\b)/i',
            '/(\bxp_\w+)/i',
        ];

        foreach ($sqlPatterns as $pattern) {
            if (preg_match($pattern, urldecode($queryString))) {
                Log::warning('SQL injection attempt in query string', [
                    'ip'    => $ip,
                    'query' => $queryString,
                ]);

                abort(400, 'Request tidak valid.');
            }
        }

        return $next($request);
    }
}
