<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
use Illuminate\Validation\Rules\Password;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }

    /**
     * Aturan password kuat yang dipakai konsisten di seluruh aplikasi.
     * Minimum 8 karakter, harus ada huruf besar, huruf kecil, angka, dan simbol.
     */
    public static function required(): Password
    {
        return Password::min(8)
            ->mixedCase()   // harus ada huruf besar dan kecil
            ->numbers()     // harus ada angka
            ->symbols()     // harus ada simbol (!@#$%^&* dll)
            ->uncompromised(); // cek di database HaveIBeenPwned (butuh internet)
    }

    /**
     * Versi tanpa uncompromised check — untuk environment tanpa akses internet.
     */
    public static function requiredOffline(): Password
    {
        return Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols();
    }
}
