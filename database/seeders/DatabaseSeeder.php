<?php

namespace Database\Seeders;

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

        // Catatan: Pengaturan perangkat (Device) kini dibuat secara dinamis 
        // melalui fitur "Tambah Perangkat" di Frontend (SetupDevice).
    }
}
