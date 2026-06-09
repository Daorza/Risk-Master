<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CriteriaController;
use App\Http\Controllers\Api\AlternativeController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\EdasController;
use App\Http\Controllers\Api\ReportController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register',        [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
    Route::post('/reset-password',  [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
    Route::post('/google',          [AuthController::class, 'googleLogin'])->name('api.auth.google');
});

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout',     [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('api.auth.logout-all');
    Route::get('/me',          [AuthController::class, 'me'])->name('api.auth.me');
});

Route::middleware('auth:sanctum')->name('api.')->group(function () {

    Route::middleware('admin')->group(function () {
        Route::apiResource('users',    UserController::class);
        Route::apiResource('criteria', CriteriaController::class);
    });

    Route::apiResource('alternatives', AlternativeController::class);
    Route::apiResource('assessments',  AssessmentController::class);

    Route::prefix('assessments/{assessment}')->group(function () {
        Route::post('calculate',                    [EdasController::class,       'calculate'])->name('assessments.calculate');
        Route::get('results',                       [EdasController::class,       'results'])->name('assessments.results');
        Route::get('values',                        [AssessmentController::class, 'showValues'])->name('assessments.values.show');
        Route::post('values',                       [AssessmentController::class, 'storeValues'])->name('assessments.values.store');
        Route::post('alternatives',                 [AssessmentController::class, 'attachAlternatives'])->name('assessments.alternatives.attach');
        Route::delete('alternatives/{alternative}', [AssessmentController::class, 'detachAlternative'])->name('assessments.alternatives.detach');
    });
});

// Public Report Routes (for easy download via browser without token)
Route::prefix('assessments/{assessment}')->group(function () {
    Route::get('report/pdf',                    [ReportController::class,     'pdf'])->name('api.assessments.report.pdf');
    // Route::get('report/pdf',                    [ReportController::class,     'pdf'])->name('assessments.report.pdf');
    Route::get('report/excel',                  [ReportController::class,     'excel'])->name('api.assessments.report.excel');
    // Route::get('report/excel',                  [ReportController::class,     'excel'])->name('assessments.report.excel');
});
