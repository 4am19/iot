<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\UserController;

// =============================================================================
//  AUTH (Basis Sesi — Stateful)
// =============================================================================
Route::post('/api/auth/login', [AuthController::class, 'login']);
Route::post('/api/auth/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/api/auth/check', [AuthController::class, 'check']);

// =============================================================================
//  DASHBOARD & FITUR (Harus Login)
// =============================================================================
Route::middleware('auth')->group(function () {

    // Data dashboard utama (sensor terbaru, histori, settings, pending command)
    Route::get('/api/dashboard-data', [SensorController::class, 'getDashboardData']);

    // Log histori (halaman History)
    Route::get('/api/logs', [SensorController::class, 'getLogs']);

    // Update pengaturan threshold / mode / nama dari Settings page
    Route::post('/api/update-setting', [SensorController::class, 'updateSetting']);

    // -----------------------------------------------------------------------
    // Command Queue — Push perintah real-time ke ESP32
    // -----------------------------------------------------------------------
    // Dashboard tekan tombol → command masuk ke antrian → ESP32 baca di request berikutnya
    Route::post('/api/device/command', [SensorController::class, 'pushCommand']);
    Route::get('/api/device/command/status', [SensorController::class, 'commandStatus']);

    // -----------------------------------------------------------------------
    // Manajemen User / Akses Keluarga
    // -----------------------------------------------------------------------
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
    Route::put('/api/users/{id}', [UserController::class, 'update']);
    Route::delete('/api/users/{id}', [UserController::class, 'destroy']);
    Route::post('/api/users/change-password', [UserController::class, 'changePassword']);
});

// =============================================================================
//  SPA CATCH-ALL (Vue Router menangani semua route tampilan)
// =============================================================================
Route::get('/login', function () {
    return view('welcome');
})->name('login');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
