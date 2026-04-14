<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorController;
use App\Http\Middleware\VerifyDeviceKey;

// ESP32 Machine Endpoints (Protected by Device Key Header)
// Secara otomatis route ini memiliki awalan /api/
Route::post('/sensor/data', [SensorController::class, 'store'])->middleware(VerifyDeviceKey::class);
