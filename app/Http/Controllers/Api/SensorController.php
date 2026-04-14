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

        return response()->json([
            'status' => 'success',
            'data' => $log
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
