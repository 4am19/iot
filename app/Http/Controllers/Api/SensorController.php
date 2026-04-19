<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommandQueue;
use App\Models\DeviceSetting;
use App\Models\SensorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SensorController extends Controller
{
    /**
     * Ambil data dashboard: sensor terbaru, histori, settings, dan pending command.
     */
    public function getDashboardData()
    {
        $setting   = DeviceSetting::first() ?? DeviceSetting::create([
            'is_auto_mode'    => true,
            'ldr_threshold'   => 50,
            'rain_threshold'  => 5,
            'manual_position' => 'Di Luar (Menjemur)',
            'device_key'      => bin2hex(random_bytes(16)),
        ]);
        $latestLog = SensorLog::latest()->first();
        $logs      = SensorLog::orderBy('id', 'desc')->take(15)->get();

        // Ambil perintah yang belum dieksekusi (untuk info di dashboard)
        $pendingCommand = CommandQueue::where('executed', false)->latest()->first();

        return response()->json([
            'setting'        => $setting,
            'latestData'     => $latestLog,
            'history'        => $logs,
            'pendingCommand' => $pendingCommand,
        ]);
    }

    /**
     * Ambil 100 log terakhir untuk halaman riwayat.
     */
    public function getLogs()
    {
        $logs = SensorLog::orderBy('id', 'desc')->take(100)->get();
        return response()->json($logs);
    }

    /**
     * Terima data sensor tunggal dari ESP32 (normal operation).
     * Juga mengembalikan settings terbaru + pending command ke ESP32.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ldr_value'          => 'required|numeric',
            'rain_percentage'    => 'required|numeric',
            'weather_condition'  => 'required|string',
            'clothesline_status' => 'required|string',
        ]);

        $log = SensorLog::create($validated);

        // Ambil settings terbaru
        $setting = DeviceSetting::first();

        // Ambil perintah tertunda (belum dieksekusi) — yang paling baru
        $pendingCommand = CommandQueue::where('executed', false)
            ->orderBy('created_at', 'asc') // FIFO: perintah pertama dieksekusi dulu
            ->first();

        // Jika ada perintah tertunda, tandai sebagai sudah dieksekusi
        // (optimistic: anggap ESP32 pasti eksekusi setelah menerima response ini)
        if ($pendingCommand) {
            $pendingCommand->update([
                'executed'    => true,
                'executed_at' => now(),
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'data'    => $log,
            'setting' => $setting ? [
                'is_auto_mode'    => $setting->is_auto_mode,
                'ldr_threshold'   => $setting->ldr_threshold,
                'rain_threshold'  => $setting->rain_threshold,
                'manual_position' => $setting->manual_position,
            ] : null,
            // Kirim pending command ke ESP32 (null jika tidak ada)
            'command' => $pendingCommand ? [
                'id'      => $pendingCommand->id,
                'action'  => $pendingCommand->command,
                'payload' => $pendingCommand->payload,
            ] : null,
        ], 200);
    }

    /**
     * Terima BATCH data sensor dari ESP32 setelah offline (Store & Forward).
     * Format: array of sensor readings yang tersimpan di LittleFS ESP32.
     */
    public function batchStore(Request $request)
    {
        // Validasi: request harus berupa array langsung (bukan object)
        // ESP32 mengirim: [{...}, {...}] bukan {"records": [...]}
        if (!is_array($request->all()) || empty($request->all())) {
            return response()->json(['error' => 'Format data tidak valid. Harus berupa array.'], 422);
        }

        // Validasi tiap record dalam array
        $request->validate([
            '*.ldr_value'          => 'required|numeric',
            '*.rain_percentage'    => 'required|numeric',
            '*.weather_condition'  => 'required|string|max:50',
            '*.clothesline_status' => 'required|string|max:50',
            // recorded_at dari ESP32 = millis() (angka integer) → selalu pakai now() saja
        ]);

        $records = collect($request->all())->map(function ($item) {
            return [
                'ldr_value'          => $item['ldr_value'],
                'rain_percentage'    => $item['rain_percentage'],
                'weather_condition'  => $item['weather_condition'],
                'clothesline_status' => $item['clothesline_status'],
                // recorded_at dari ESP32 adalah millis() (bukan timestamp),
                // jadi kita gunakan waktu server saat batch diterima
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        })->toArray();

        // Mass insert — jauh lebih efisien dari loop create()
        SensorLog::insert($records);

        return response()->json([
            'status'  => 'success',
            'message' => count($records) . ' data offline berhasil disinkronkan.',
            'count'   => count($records),
        ], 201);
    }

    /**
     * Update pengaturan dari dashboard (threshold, mode, dll).
     */
    public function updateSetting(Request $request)
    {
        $setting = DeviceSetting::first();
        if ($setting) {
            $setting->update($request->only([
                'is_auto_mode',
                'ldr_threshold',
                'rain_threshold',
                'manual_position',
                'owner_name',
            ]));
        }
        return response()->json(['status' => 'success', 'setting' => $setting]);
    }

    /**
     * Push perintah dari dashboard ke antrian (command queue).
     * ESP32 akan membaca perintah ini pada request HTTP berikutnya.
     */
    public function pushCommand(Request $request)
    {
        $validated = $request->validate([
            'command' => 'required|string|in:move_in,move_out,set_auto,set_manual,reboot',
            'payload' => 'nullable|array',
        ]);

        // Hapus perintah lama yang belum dieksekusi supaya tidak tumpuk
        CommandQueue::where('executed', false)->delete();

        $command = CommandQueue::create([
            'command' => $validated['command'],
            'payload' => $validated['payload'] ?? null,
            'executed' => false,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Perintah dikirim ke antrian. ESP32 akan menerimanya pada koneksi berikutnya.',
            'command' => $command,
        ]);
    }

    /**
     * Status perintah — apakah sudah dieksekusi ESP32.
     */
    public function commandStatus()
    {
        $pending = CommandQueue::where('executed', false)->latest()->first();
        $last    = CommandQueue::where('executed', true)->latest()->first();

        return response()->json([
            'pending_command' => $pending,
            'last_executed'   => $last,
        ]);
    }
}
