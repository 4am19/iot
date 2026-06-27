<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommandQueue;
use App\Models\Device;
use App\Models\SensorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SensorController extends Controller
{
    /**
     * Dapatkan perangkat milik user (atau member) berdasarkan ID, atau ambil perangkat pertama.
     */
    private function getActiveDevice($deviceId = null)
    {
        $user = Auth::user();
        if ($deviceId) {
            return $user->devices()->where('devices.id', $deviceId)->first();
        }
        return $user->devices()->first();
    }

    /**
     * Ambil data dashboard: sensor terbaru, histori, settings, dan pending command.
     */
    public function getDashboardData(Request $request)
    {
        $device = $this->getActiveDevice($request->query('device_id'));

        if (!$device) {
            return response()->json(['error' => 'Tidak ada perangkat terhubung.'], 404);
        }

        $latestLog = $device->logs()->latest()->first();
        $logs      = $device->logs()->orderBy('id', 'desc')->take(15)->get();

        // Ambil perintah yang belum dieksekusi (untuk info di dashboard, jika masih pakai polling)
        $pendingCommand = CommandQueue::where('device_id', $device->id)
                                      ->where('executed', false)
                                      ->latest()->first();

        return response()->json([
            'setting'        => $device,
            'latestData'     => $latestLog,
            'history'        => $logs,
            'pendingCommand' => $pendingCommand,
        ]);
    }

    /**
     * Ambil 100 log terakhir untuk halaman riwayat.
     */
    public function getLogs(Request $request)
    {
        $device = $this->getActiveDevice($request->query('device_id'));

        if (!$device) {
            return response()->json(['error' => 'Tidak ada perangkat terhubung.'], 404);
        }

        $logs = $device->logs()->orderBy('id', 'desc')->take(100)->get();
        return response()->json($logs);
    }

    /**
     * Terima data sensor tunggal dari ESP32 (normal operation).
     * Wajib mengirimkan mac_address.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mac_address'        => 'required|string',
            'ldr_value'          => 'required|numeric',
            'rain_percentage'    => 'required|numeric',
            'weather_condition'  => 'required|string',
            'clothesline_status' => 'required|string',
        ]);

        $device = Device::where('mac_address', $validated['mac_address'])->first();

        if (!$device) {
            // Auto-register device jika belum ada (opsional, untuk kemudahan)
            $device = Device::create([
                'mac_address' => $validated['mac_address'],
                'name' => 'Jemuran ESP32'
            ]);
        }

        $latestLog = $device->logs()->latest()->first();

        if ($latestLog && $latestLog->clothesline_status === $validated['clothesline_status']) {
            $latestLog->update([
                'ldr_value' => $validated['ldr_value'],
                'rain_percentage' => $validated['rain_percentage'],
                'weather_condition' => $validated['weather_condition']
            ]);
            $log = $latestLog;
        } else {
            $log = $device->logs()->create([
                'ldr_value' => $validated['ldr_value'],
                'rain_percentage' => $validated['rain_percentage'],
                'weather_condition' => $validated['weather_condition'],
                'clothesline_status' => $validated['clothesline_status']
            ]);
        }

        // Ambil perintah tertunda
        $pendingCommand = CommandQueue::where('device_id', $device->id)
            ->where('executed', false)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($pendingCommand) {
            $pendingCommand->update([
                'executed'    => true,
                'executed_at' => now(),
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'data'    => $log,
            'setting' => [
                'is_auto_mode'    => $device->is_auto_mode,
                'ldr_threshold'   => $device->ldr_threshold,
                'rain_threshold'  => $device->rain_threshold,
                'manual_position' => $device->manual_position,
            ],
            'command' => $pendingCommand ? [
                'id'      => $pendingCommand->id,
                'action'  => $pendingCommand->command,
                'payload' => $pendingCommand->payload,
            ] : null,
        ], 200);
    }

    /**
     * Update pengaturan dari dashboard (threshold, mode, dll).
     */
    public function updateSetting(Request $request)
    {
        $device = $this->getActiveDevice($request->device_id);

        if ($device) {
            $device->update($request->only([
                'is_auto_mode',
                'ldr_threshold',
                'rain_threshold',
                'manual_position',
                'name',
            ]));
            return response()->json(['status' => 'success', 'setting' => $device]);
        }
        return response()->json(['error' => 'Perangkat tidak ditemukan'], 404);
    }

    /**
     * Push perintah dari dashboard ke antrian (command queue) fallback (jika tidak pakai Firebase).
     */
    public function pushCommand(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'nullable|integer',
            'command' => 'required|string|in:move_in,move_out,set_auto,set_manual,reboot',
            'payload' => 'nullable|array',
        ]);

        $device = $this->getActiveDevice($request->device_id);
        if (!$device) {
            return response()->json(['error' => 'Perangkat tidak ditemukan'], 404);
        }

        // Hapus perintah lama
        CommandQueue::where('device_id', $device->id)->where('executed', false)->delete();

        $command = CommandQueue::create([
            'device_id' => $device->id,
            'command' => $validated['command'],
            'payload' => $validated['payload'] ?? null,
            'executed' => false,
        ]);

        // Otomatis matikan Auto Mode jika manual
        if ($validated['command'] === 'move_in') {
            $device->update(['is_auto_mode' => false, 'manual_position' => 'Di Dalam']);
        } elseif ($validated['command'] === 'move_out') {
            $device->update(['is_auto_mode' => false, 'manual_position' => 'Di Luar (Menjemur)']);
        } elseif ($validated['command'] === 'set_manual') {
            $device->update(['is_auto_mode' => false]);
        } elseif ($validated['command'] === 'set_auto') {
            $device->update(['is_auto_mode' => true]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Perintah dikirim ke antrian.',
            'command' => $command,
        ]);
    }
}
