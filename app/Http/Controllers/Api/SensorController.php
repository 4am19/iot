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
use Illuminate\Support\Facades\Cache;
use App\Models\AuditLog;

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
     * Ambil log aktivitas gabungan (pergerakan + audit login).
     */
    public function getActivityLogs(Request $request)
    {
        $sensorLogs = SensorLog::orderBy('id', 'desc')->take(100)->get()->map(function ($log) {
            $log->type = 'pergerakan';
            return $log;
        });

        // Hanya admin yang bisa melihat audit log (asumsi role ada di tabel users, tapi untuk amannya kita sertakan selalu jika user terautentikasi dan admin)
        $auditLogs = collect();
        if (Auth::check() && Auth::user()->role === 'admin') {
            $auditLogs = AuditLog::with('user')->orderBy('id', 'desc')->take(50)->get()->map(function ($log) {
                $log->type = 'audit';
                return $log;
            });
        }

        // Gabungkan dan urutkan berdasarkan created_at descending
        $combinedLogs = $sensorLogs->concat($auditLogs)->sortByDesc(function ($item) {
            return $item->created_at;
        })->values()->all();

        return response()->json($combinedLogs);
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

        $setting = DeviceSetting::first();
        $triggerSource = 'otomatis';
        
        if ($request->input('button_pressed')) {
            $triggerSource = 'manual_fisik';
        } elseif ($setting && !$setting->is_auto_mode) {
            $triggerSource = 'manual_dashboard';
        }
        $validated['trigger_source'] = $triggerSource;

        $latestLog = SensorLog::latest()->first();

        if ($latestLog && $latestLog->clothesline_status === $validated['clothesline_status']) {
            $latestLog->update($validated);
            $latestLog->touch(); // Wajib! Memaksa updated_at diperbarui meskipun nilai ldr/hujan sama persis
            $log = $latestLog;
        } else {
            $log = SensorLog::create($validated);
            // --- RULE ENGINE: NOTIFIKASI PERUBAHAN OTOMATIS ---
            // Kirim notifikasi jika terjadi perubahan posisi secara otomatis (bukan manual)
            if (!$request->input('button_pressed')) {
                $this->broadcastWhatsAppAlert($validated);
            }
        }

        // Settings sudah diambil di atas


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
    private function broadcastWhatsAppAlert($data)
    {
        $botUrl = env('WA_BOT_URL', 'http://localhost:3000/send-broadcast');
        $usersWithPhone = User::whereNotNull('phone')->where('phone', '!=', '')->pluck('phone')->toArray();

        if (empty($usersWithPhone)) {
            return; // Tidak ada nomor WA yang terdaftar
        }

        $status = $data['clothesline_status'];
        $weather = $data['weather_condition'];
        $rain = $data['rain_percentage'] ?? 0;
        $ldr = $data['ldr_value'] ?? 0;

        $timestamp = now()->timezone('Asia/Jakarta')->format('d M Y, H:i \W\I\B');
        $isEmergency = $status === 'Di Dalam';
        
        $header = $isEmergency ? "🔴 PERINGATAN SISTEM: CUACA BURUK" : "🟢 INFO SISTEM: CUACA CERAH";

        $message = "*{$header}*\n"
                 . "━━━━━━━━━━━━━━━━━━━━━━\n"
                 . "Waktu: {$timestamp}\n"
                 . "Sistem: Smart Clothesline (Node-01)\n"
                 . "━━━━━━━━━━━━━━━━━━━━━━\n"
                 . "*Data Sensor Real-time:*\n"
                 . "☁️ Kondisi Cuaca: *" . strtoupper($weather) . "*\n"
                 . "💧 Sensor Hujan: *{$rain}%*\n"
                 . "☀️ Sensor Cahaya: *{$ldr}*\n"
                 . "━━━━━━━━━━━━━━━━━━━━━━\n\n";

        if ($isEmergency) {
            $message .= "⚠️ *Tindakan Otomatis*\n"
                     . "Sistem mendeteksi cuaca buruk. Jemuran telah *ditarik ke dalam ruangan* secara otomatis untuk mengamankan pakaian Anda.\n\n";
        } else {
            $message .= "✅ *Tindakan Otomatis*\n"
                     . "Cuaca sudah kembali normal. Jemuran telah *dikeluarkan kembali* secara otomatis untuk melanjutkan penjemuran.\n\n";
        }

        $message .= "_- Pesan Otomatis dari Smart Clothesline IoT -_";

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
            $oldLdr = $setting->ldr_threshold;
            $oldRain = $setting->rain_threshold;

            $setting->update($request->only([
                'is_auto_mode',
                'ldr_threshold',
                'rain_threshold',
                'manual_position',
                'owner_name',
            ]));

            if ($request->has('ldr_threshold') && $oldLdr != $request->ldr_threshold) {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => "Ubah Kalibrasi Cahaya: $oldLdr% -> {$request->ldr_threshold}%",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            if ($request->has('rain_threshold') && $oldRain != $request->rain_threshold) {
                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => "Ubah Kalibrasi Hujan: $oldRain% -> {$request->rain_threshold}%",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
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

    /**
     * Endpoint untuk dipanggil oleh Node.js Bot setiap 10 detik.
     * Mendeteksi jika ESP32 terputus (mati lampu/internet) dan memicu WhatsApp Alert.
     */
    public function healthCheck()
    {
        $latestLog = SensorLog::latest()->first();
        if (!$latestLog) {
            return response()->json(['status' => 'no_data']);
        }

        // Menggunakan abs() untuk memastikan nilai positif, atau membalik urutannya
        $secondsSinceLastUpdate = abs(now()->diffInSeconds($latestLog->updated_at));
        $isCurrentlyOffline = $secondsSinceLastUpdate > 15; // Timeout 15 detik

        // Ambil status offline sebelumnya dari Cache (default: false / online)
        $wasOffline = Cache::get('device_is_offline', false);

        if ($isCurrentlyOffline && !$wasOffline) {
            // TERPUTUS: Status berubah dari Online -> Offline
            Cache::put('device_is_offline', true, now()->addDays(7));
            $this->broadcastOfflineAlert(true, $latestLog->updated_at);
            return response()->json(['status' => 'went_offline', 'last_seen' => $latestLog->updated_at]);
        } 
        elseif (!$isCurrentlyOffline && $wasOffline) {
            // PULIH: Status berubah dari Offline -> Online
            Cache::put('device_is_offline', false, now()->addDays(7));
            $this->broadcastOfflineAlert(false, $latestLog->updated_at);
            return response()->json(['status' => 'came_online', 'last_seen' => $latestLog->updated_at]);
        }

        return response()->json([
            'status' => $isCurrentlyOffline ? 'offline' : 'online',
            'seconds_since_last_update' => $secondsSinceLastUpdate
        ]);
    }

    /**
     * Broadcast pesan WhatsApp khusus untuk kegagalan sistem (Stress Test)
     */
    private function broadcastOfflineAlert($isOffline, $lastSeen)
    {
        $botUrl = env('WA_BOT_URL', 'http://localhost:3000/send-broadcast');
        $usersWithPhone = User::whereNotNull('phone')->where('phone', '!=', '')->pluck('phone')->toArray();

        if (empty($usersWithPhone)) {
            return;
        }

        $timestamp = now()->timezone('Asia/Jakarta')->format('d M Y, H:i \W\I\B');
        $lastSeenFormatted = $lastSeen->timezone('Asia/Jakarta')->format('d M Y, H:i:s \W\I\B');

        if ($isOffline) {
            $message = "*🔴 CRITICAL INCIDENT: KONEKSI TERPUTUS*\n"
                     . "━━━━━━━━━━━━━━━━━━━━━━\n"
                     . "Waktu Insiden: {$timestamp}\n"
                     . "Sistem: Smart Clothesline (Node-01)\n"
                     . "━━━━━━━━━━━━━━━━━━━━━━\n"
                     . "⚠️ *Peringatan Kegagalan Perangkat*\n"
                     . "Server mendeteksi bahwa perangkat ESP32 telah kehilangan koneksi ke jaringan pusat. Pemantauan cuaca otomatis saat ini **TIDAK AKTIF**.\n\n"
                     . "Terakhir aktif: {$lastSeenFormatted}\n\n"
                     . "Tindakan yang disarankan:\n"
                     . "1. Periksa ketersediaan daya (mati lampu/kabel tercabut).\n"
                     . "2. Pastikan sinyal Wi-Fi di area penjemuran stabil.\n\n"
                     . "_- IT Operations & Control Center -_";
        } else {
            $message = "*🟢 INFO SISTEM: KONEKSI PULIH*\n"
                     . "━━━━━━━━━━━━━━━━━━━━━━\n"
                     . "Waktu Pemulihan: {$timestamp}\n"
                     . "Sistem: Smart Clothesline (Node-01)\n"
                     . "━━━━━━━━━━━━━━━━━━━━━━\n"
                     . "✅ *Perangkat Kembali Online*\n"
                     . "Perangkat ESP32 berhasil terhubung kembali ke jaringan pusat server. Seluruh sistem pemantauan dan kendali otomatis telah **BEROPERASI NORMAL**.\n\n"
                     . "_- IT Operations & Control Center -_";
        }

        try {
            Http::timeout(3)->post($botUrl, [
                'numbers' => $usersWithPhone,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim WA Offline Alert: ' . $e->getMessage());
        }
    }
}
