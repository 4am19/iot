<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SensorController;

// Auth Routes (Session based)
Route::post('/api/auth/login', [AuthController::class, 'login']);
Route::post('/api/auth/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/api/auth/check', [AuthController::class, 'check']);

// UI Endpoints (Session protected)
Route::middleware('auth')->group(function () {
    Route::get('/api/dashboard-data', [SensorController::class, 'getDashboardData']);
    Route::get('/api/logs', [SensorController::class, 'getLogs']);
    Route::post('/api/update-setting', [SensorController::class, 'updateSetting']);
    
    // User Management Route
    Route::get('/api/users', [\App\Http\Controllers\Api\UserController::class, 'index']);
    Route::post('/api/users', [\App\Http\Controllers\Api\UserController::class, 'store']);
    Route::put('/api/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'update']);
    Route::delete('/api/users/{id}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);
});

Route::get('/login', function () {
    return view('welcome');
})->name('login');

// Biarkan Vue SPA menangani semua route view.
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
