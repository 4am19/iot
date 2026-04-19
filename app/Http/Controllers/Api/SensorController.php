<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceSetting;
use App\Models\SensorLog;

class SensorController extends Controller
{
    public function getDashboardData()
    {
        $setting = DeviceSetting::first() ?? DeviceSetting::create();
        $latestLog = SensorLog::latest()->first();
        $logs = SensorLog::orderBy('id', 'desc')->take(15)->get();

        return response()->json([
            'setting' => $setting,
            'latestData' => $latestLog,
            'history' => $logs
        ]);
    }

    public function getLogs()
    {
        // Pagination atau ambil 100 terakhir untuk tabel riwayat
        $logs = SensorLog::orderBy('id', 'desc')->take(100)->get();
        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ldr_value' => 'required|numeric',
            'rain_percentage' => 'required|numeric',
            'weather_condition' => 'required|string',
            'clothesline_status' => 'required|string'
        ]);

        $log = SensorLog::create($validated);

        // Kirim balik pengaturan terbaru ke ESP32 agar threshold selalu sinkron
        $setting = DeviceSetting::first();

        return response()->json([
            'status' => 'success',
            'data' => $log,
            'setting' => $setting ? [
                'is_auto_mode'    => $setting->is_auto_mode,
                'ldr_threshold'   => $setting->ldr_threshold,
                'rain_threshold'  => $setting->rain_threshold,
                'manual_position' => $setting->manual_position,
            ] : null
        ], 200);
    }
    
    public function updateSetting(Request $request)
    {
        $setting = DeviceSetting::first();
        if ($setting) {
            $setting->update($request->all());
        }
        return response()->json(['status' => 'success', 'setting' => $setting]);
    }
}
