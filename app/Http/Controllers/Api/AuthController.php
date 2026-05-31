<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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
            Str::lower($request->input('email')) . '|' . $request->ip()
        );
    }

}
