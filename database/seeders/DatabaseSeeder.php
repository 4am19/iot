<?php

namespace Database\Seeders;

use App\Models\DeviceSetting;
use App\Models\SensorLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================
        // 1. Buat Akun ADMIN UTAMA (default pertama kali)
        //    ⚠️  Segera ubah password setelah login pertama!
        // =====================================================
        User::firstOrCreate(
            ['email' => 'admin@jemuran.com'],
            [
                'name'     => 'Admin Utama',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        // =====================================================
        // 2. Inisialisasi Pengaturan Dasar Perangkat
        // =====================================================
        DeviceSetting::firstOrCreate(
            ['id' => 1],
            [
                'is_auto_mode'    => true,
                'ldr_threshold'   => 50,
                'rain_threshold'  => 5,
                'manual_position' => 'Di Luar (Menjemur)',
                'device_key'      => bin2hex(random_bytes(16)),
            ]
        );

        // Dummy data SensorLog telah dihapus sesuai permintaan.
    }
}
