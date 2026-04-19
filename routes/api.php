<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\VerifyDeviceKey;

// =============================================================================
//  GRUP 1: ENDPOINT MESIN — ESP32 (dilindungi X-API-KEY header)
// =============================================================================

// Kirim data sensor tunggal (operasi normal)
Route::post('/sensor/data', [SensorController::class, 'store'])
    ->middleware(VerifyDeviceKey::class);

// Kirim batch data sensor setelah offline (Store & Forward)
Route::post('/sensor/data/batch', [SensorController::class, 'batchStore'])
    ->middleware(VerifyDeviceKey::class);

// =============================================================================
//  GRUP 2: ENDPOINT PUBLIK TERBATAS — Auth (login, cek sesi)
// =============================================================================

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::get('/auth/check', [AuthController::class, 'check']);

// =============================================================================
//  GRUP 3: ENDPOINT DASHBOARD — Harus Login (dilindungi sesi)
// =============================================================================

Route::middleware('auth:web')->group(function () {

    // --- Data Dashboard & Sensor ---
    Route::get('/dashboard-data', [SensorController::class, 'getDashboardData']);
    Route::get('/sensor/logs', [SensorController::class, 'getLogs']);
    Route::post('/update-setting', [SensorController::class, 'updateSetting']);

    // --- Command Queue (Push perintah ke ESP32 dari dashboard) ---
    Route::post('/device/command', [SensorController::class, 'pushCommand']);
    Route::get('/device/command/status', [SensorController::class, 'commandStatus']);

    // --- Manajemen User / Akses Keluarga ---
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/users/change-password', [UserController::class, 'changePassword']);
});
