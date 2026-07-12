<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommandQueue;
use App\Models\DeviceSetting;
use App\Models\SensorLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
            'server_time'    => now()->toIso8601String(),
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
            'is_auto_mode'       => 'nullable|boolean',
        ]);

        $latestLog = SensorLog::latest()->first();

        if ($latestLog && $latestLog->clothesline_status === $validated['clothesline_status']) {
            $latestLog->update($validated);
            $log = $latestLog;
        } else {
            $log = SensorLog::create($validated);
            
            // --- RULE ENGINE: KONDISI JEMURAN DITARIK KARENA CUACA ---
            // Hanya kirim notifikasi jika status baru adalah "Di Dalam" dan bukan karena mode manual
            if ($validated['clothesline_status'] === 'Di Dalam' && !$request->input('button_pressed')) {
                $this->broadcastWhatsAppAlert($validated['weather_condition']);
            }
        }

        // Ambil settings terbaru
        $setting = DeviceSetting::first();

        // (Logika override dari ESP32 dihapus karena menyebabkan race condition:
        // status tertinggal dari ESP32 menimpa pengaturan baru dari dashboard)
        // UPDATE: Gunakan flag button_pressed eksplisit dari ESP32
        if ($request->input('button_pressed') && $setting) {
            $setting->update([
                'is_auto_mode'    => $validated['is_auto_mode'],
                'manual_position' => $validated['clothesline_status']
            ]);
        }
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
     * Broadcast pesan WhatsApp ke semua user yang memiliki nomor telepon terdaftar.
     */
    private function broadcastWhatsAppAlert($weather)
    {
        $botUrl = env('WA_BOT_URL', 'http://localhost:3000/send-broadcast');
        $usersWithPhone = User::whereNotNull('phone')->where('phone', '!=', '')->pluck('phone')->toArray();

        if (empty($usersWithPhone)) {
            return; // Tidak ada nomor WA yang terdaftar
        }

        $message = "🚨 *PERINGATAN DINI CUACA!*\n\n"
                 . "Kondisi saat ini: *{$weather}*\n"
                 . "Sistem telah otomatis menarik jemuran ke dalam ruangan untuk melindungi pakaian Anda.\n\n"
                 . "_- Smart Clothesline IoT_";

        try {
            // Kirim secara asinkron (timeout 2 detik agar tidak menghalangi response ESP32)
            Http::timeout(2)->post($botUrl, [
                'numbers' => $usersWithPhone,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            // Abaikan error jika bot WA mati agar ESP32 tidak menerima error 500
            \Log::error('Gagal mengirim WhatsApp alert: ' . $e->getMessage());
        }
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
            '*.recorded_at'        => 'nullable|numeric',
        ]);

        $latestLog = SensorLog::latest()->first();
        $count = 0;

        foreach ($request->all() as $item) {
            if ($latestLog && $latestLog->clothesline_status === $item['clothesline_status']) {
                $latestLog->update([
                    'ldr_value'          => $item['ldr_value'],
                    'rain_percentage'    => $item['rain_percentage'],
                    'weather_condition'  => $item['weather_condition'],
                    'updated_at'         => now(),
                ]);
            } else {
                $latestLog = SensorLog::create([
                    'ldr_value'          => $item['ldr_value'],
                    'rain_percentage'    => $item['rain_percentage'],
                    'weather_condition'  => $item['weather_condition'],
                    'clothesline_status' => $item['clothesline_status'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }
            $count++;
        }

        return response()->json([
            'status'  => 'success',
            'message' => $count . ' data offline berhasil disinkronkan.',
            'count'   => $count,
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
            'command' => 'required|string|in:move_in,move_out,set_auto,set_manual,reboot,reset_wifi',
            'payload' => 'nullable|array',
        ]);

        // Hapus perintah lama yang belum dieksekusi supaya tidak tumpuk
        CommandQueue::where('executed', false)->delete();

        $command = CommandQueue::create([
            'command' => $validated['command'],
            'payload' => $validated['payload'] ?? null,
            'executed' => false,
        ]);

        // Otomatis matikan Auto Mode jika user mengirim perintah manual (agar ESP32 tidak langsung revert)
        $setting = DeviceSetting::first();
        if ($setting) {
            if ($validated['command'] === 'move_in') {
                $setting->update(['is_auto_mode' => false, 'manual_position' => 'Di Dalam']);
            } elseif ($validated['command'] === 'move_out') {
                $setting->update(['is_auto_mode' => false, 'manual_position' => 'Di Luar (Menjemur)']);
            } elseif ($validated['command'] === 'set_manual') {
                $setting->update(['is_auto_mode' => false]);
            } elseif ($validated['command'] === 'set_auto') {
                $setting->update(['is_auto_mode' => true]);
            }
        }

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
