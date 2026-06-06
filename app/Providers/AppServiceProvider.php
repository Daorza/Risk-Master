<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Models\Assessment;
use App\Observers\AssessmentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Berlaku di semua tempat yang pakai Password::defaults()
        Password::defaults(function () {
            return Password::min(8)
                ->mixedCase()   // harus ada huruf besar DAN kecil
                ->numbers()     // harus ada angka
                ->symbols();    // harus ada simbol (!@#$% dll)
        });

        Assessment::observe(AssessmentObserver::class);
    }
}
