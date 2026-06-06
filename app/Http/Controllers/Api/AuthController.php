<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->error(
                "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
                429,
            );
        }

        $user = User::firstWhere('email', $validated['email']);

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak valid.'],
            ]);
        }

        RateLimiter::clear($throttleKey);

        $deviceName = $validated['device_name'] ?? 'API Client';
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken(
            name: $deviceName,
            abilities: ['*'],
            expiresAt: now()->addDays(30),
        );

        AuditLog::record(
            action: 'login',
            tableName: 'users',
            recordId: $user->id,
            newData: [
                'device_name' => $deviceName,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        );

        return $this->success(
            [
                'token' => $token->plainTextToken,
                'type' => 'Bearer',
                'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
                'user' => $this->userPayload($user),
            ],
            'Login berhasil',
            200,
        );
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'              => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'string'],
            'device_name'           => ['sometimes', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'user', // default selalu user, tidak bisa pilih role
        ]);

        AuditLog::record(
            action: 'register',
            tableName: 'users',
            recordId: $user->id,
            newData: [
                'name'       => $user->name,
                'email'      => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        );

        return $this->issueToken($user, $request, 'register', 201);
    }

    // ── Forgot Password ───────────────────────────────────────────────────────

    public function forgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Rate limit: maks 3 request per 10 menit per email+IP
        $throttleKey = 'forgot-password:' . Str::lower($validated['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return $this->error(
                "Terlalu banyak permintaan. Coba lagi dalam {$seconds} detik.",
                429,
            );
        }

        RateLimiter::hit($throttleKey, 600);

        // Selalu return success meski email tidak ditemukan
        // untuk mencegah user enumeration attack
        $user = User::firstWhere('email', $validated['email']);

        if ($user) {
            // Generate OTP 6 digit
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Simpan ke tabel password_reset_tokens
            // Hash OTP agar tidak tersimpan plaintext di DB
            DB::table('password_reset_tokens')->upsert(
                [
                    'email'      => $validated['email'],
                    'token'      => Hash::make($otp),
                    'created_at' => now(),
                ],
                uniqueBy: ['email'],
                update: ['token', 'created_at'],
            );

            // ── Untuk development: print OTP di log ──────────────────────────
            // Ganti bagian ini dengan pengiriman email sungguhan saat production
            Log::info('OTP Reset Password', [
                'email' => $validated['email'],
                'otp'   => $otp,       // ← bisa dilihat di storage/logs/laravel.log
            ]);

            // ── Untuk production (uncomment saat email sudah dikonfigurasi): ──
            // \Illuminate\Support\Facades\Mail::to($validated['email'])
            //     ->send(new \App\Mail\ResetPasswordOtp($otp));
        }

        return $this->success(
            null,
            'Kode reset telah dikirim ke email Anda jika terdaftar.',
        );
    }

    // ── Reset Password ────────────────────────────────────────────────────────

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'                 => ['required', 'string', 'email'],
            'token'                 => ['required', 'string'],
            'password'              => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required', 'string'],
        ]);

        // Cari token di DB
        $record = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        // Validasi token ada, cocok, dan belum expired (15 menit)
        if (
            ! $record
            || ! Hash::check($validated['token'], $record->token)
            || Carbon::parse($record->created_at)->addMinutes(15)->isPast()
        ) {
            return $this->error('Kode reset tidak valid atau sudah kadaluarsa.', 422);
        }

        // Update password
        $user = User::firstWhere('email', $validated['email']);

        if (! $user) {
            return $this->error('User tidak ditemukan.', 404);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        // Hapus token setelah dipakai (single use)
        DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->delete();

        // Revoke semua token Sanctum yang aktif untuk keamanan
        $user->tokens()->delete();

        AuditLog::record(
            action: 'password_reset',
            tableName: 'users',
            recordId: $user->id,
            newData: ['ip_address' => $request->ip()],
        );

        return $this->success(null, 'Password berhasil direset. Silakan login kembali.');
    }

    // ── Google Login ──────────────────────────────────────────────────────────

    public function googleLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_token'    => ['required', 'string'],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ]);

        // Verifikasi id_token ke Google API
        try {
            $client = new \Google\Client([
                'client_id' => config('services.google.client_id'),
            ]);

            $payload = $client->verifyIdToken($validated['id_token']);

            if (! $payload) {
                return $this->error('Token Google tidak valid.', 401);
            }
        } catch (\Throwable $e) {
            Log::error('Google token verification failed', [
                'error' => $e->getMessage(),
                'ip'    => $request->ip(),
            ]);

            return $this->error('Verifikasi Google gagal. Coba lagi.', 401);
        }

        $googleEmail = $payload['email'] ?? null;
        $googleName  = $payload['name'] ?? null;
        $googleId    = $payload['sub'] ?? null; // Google user ID unik

        if (! $googleEmail) {
            return $this->error('Email tidak dapat diambil dari akun Google.', 422);
        }

        // Cari user berdasarkan email, atau buat baru jika belum ada
        $user = User::firstWhere('email', $googleEmail);

        if (! $user) {
            $user = User::create([
                'name'              => $googleName ?? $googleEmail,
                'email'             => $googleEmail,
                'password'          => Hash::make(Str::random(32)), // password random karena login via Google
                'role'              => 'user',
                'email_verified_at' => now(), // Google sudah verifikasi email-nya
            ]);

            AuditLog::record(
                action: 'register_google',
                tableName: 'users',
                recordId: $user->id,
                newData: [
                    'email'      => $googleEmail,
                    'google_id'  => $googleId,
                    'ip_address' => $request->ip(),
                ],
            );
        }

        return $this->issueToken($user, $request, 'login_google');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        AuditLog::record(
            action: 'logout',
            tableName: 'users',
            recordId: $user->id,
            newData: [
                'ip_address' => $request->ip(),
            ],
        );

        $user->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        return $this->success(
            null,
            'Logout berhasil.',
        );
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $user = $request->user();
        $tokenCount = $user->tokens()->count();

        AuditLog::record(
            action: 'logout_all',
            tableName: 'users',
            recordId: $user->id,
            newData: [
                'revoked_count' => $tokenCount,
                'ip_address' => $request->ip(),
            ],
         );

        $user->tokens()->delete();

        return $this->success(
            ['revoked_count' => $tokenCount],
            "Berhasil logout dari {$tokenCount} sesi.",
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(
            $this->userPayload($request->user())
        );
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Buat Sanctum token dan return response standar.
     * Dipakai oleh login, register, dan googleLogin agar konsisten.
     */
    private function issueToken(
        User $user,
        Request $request,
        string $auditAction,
        int $statusCode = 200,
    ): JsonResponse {
        $deviceName = $request->input('device_name', 'API Client');

        // Satu device hanya boleh punya satu token aktif
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken(
            name: $deviceName,
            abilities: ['*'],
            expiresAt: now()->addDays(30),
        );

        AuditLog::record(
            action: $auditAction,
            tableName: 'users',
            recordId: $user->id,
            newData: [
                'device_name' => $deviceName,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ],
        );

        return $this->success(
            [
                'token'      => $token->plainTextToken,
                'type'       => 'Bearer',
                'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
                'user'       => $this->userPayload($user),
            ],
            match ($auditAction) {
                'login'          => 'Login berhasil.',
                'register'       => 'Registrasi berhasil.',
                'login_google'   => 'Login dengan Google berhasil.',
                'register_google'=> 'Akun Google berhasil didaftarkan.',
                default          => 'Berhasil.',
            },
            $statusCode,
        );
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at->toIso8601String(),
        ];
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('email', '')) . '|' . $request->ip()
        );
    }

}
