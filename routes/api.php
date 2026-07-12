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

// Endpoint Health Check untuk mendeteksi offline (dipanggil oleh Node.js bot)
Route::get('/device/health-check', [SensorController::class, 'healthCheck']);

// Semua route dashboard dan SPA menggunakan web middleware dan didefinisikan di routes/web.php
