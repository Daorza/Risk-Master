<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CriteriaController;
use App\Http\Controllers\Api\AlternativeController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\EdasController;
use App\Http\Controllers\Api\ReportController;

// ── Public ───────────────────────────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
});

// ── Auth protected ────────────────────────────────────────────────────────────

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout',     [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('api.auth.logout-all');
    Route::get('/me',          [AuthController::class, 'me'])->name('api.auth.me');
});

// ── Resource routes ───────────────────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::apiResource('users',    UserController::class);

        Route::apiResource('criteria', CriteriaController::class);
    });

    // Semua user yang ter-autentikasi
    Route::apiResource('alternatives', AlternativeController::class);

    Route::apiResource('assessments', AssessmentController::class);

    // Nested assessment routes
    Route::prefix('assessments/{assessment}')->group(function () { // Fix 2: tutup kurung kurawal
        Route::post('calculate',                    [EdasController::class,      'calculate']);
        Route::get('results',                       [EdasController::class,      'results']);
        Route::get('values',                        [AssessmentController::class, 'showValues']); // Fix 3: showValues bukan show
        Route::post('values',                       [AssessmentController::class, 'storeValues']);
        Route::post('alternatives',                 [AssessmentController::class, 'attachAlternatives']);
        Route::delete('alternatives/{alternative}', [AssessmentController::class, 'detachAlternative']);
        Route::get('report/pdf',                    [ReportController::class,    'pdf']);
        Route::get('report/excel',                  [ReportController::class,    'excel']);
    });
});
