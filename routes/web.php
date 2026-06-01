<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AssessmentController;
use App\Http\Controllers\Web\AlternativeController;
use App\Http\Controllers\Web\Admin\UserController as AdminUserController;
use App\Http\Controllers\Web\Admin\CriteriaController as AdminCriteriaController;

// ── Root ────────────────────────────────────────────────────────────────────

Route::get('/', fn() => redirect()->route('dashboard'));

// ── Auth (Session) ───────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Authenticated Web (Session) ──────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Alternatives
    Route::resource('alternatives', AlternativeController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Assessments
    Route::resource('assessments', AssessmentController::class);

    Route::post('assessments/{assessment}/calculate', [AssessmentController::class, 'calculate'])
        ->name('assessments.calculate');
    Route::get('assessments/{assessment}/results', [AssessmentController::class, 'results'])
        ->name('assessments.results');
    Route::get('assessments/{assessment}/report/pdf', [AssessmentController::class, 'reportPdf'])
        ->name('assessments.report.pdf');

    // Input nilai matrix (web)
    Route::get('assessments/{assessment}/values', [AssessmentController::class, 'editValues'])
        ->name('assessments.values.edit');
    Route::post('assessments/{assessment}/values', [AssessmentController::class, 'storeValues'])
        ->name('assessments.values.store');

    // Admin only
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', AdminUserController::class);
        Route::resource('criteria', AdminCriteriaController::class)
            ->parameters(['criteria' => 'criterium']);
    });
});
