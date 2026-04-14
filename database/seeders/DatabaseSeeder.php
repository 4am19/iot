<?php

namespace Database\Seeders;

use App\Models\DeviceSetting;
use App\Models\SensorLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Inisialisasi Pengaturan Dasar
        DeviceSetting::create([
            'is_auto_mode' => true,
            'ldr_threshold' => 50,
            'rain_threshold' => 5,
            'manual_position' => 'Di Luar (Menjemur)'
        ]);

        // 2. Data Dummy Historis untuk Grafik 24 Jam
        $statuses = [
            ['ldr' => 80, 'rain' => 0, 'weather' => 'Cerah Terik', 'pos' => 'Di Luar (Menjemur)'],
            ['ldr' => 60, 'rain' => 0, 'weather' => 'Cerah Berawan', 'pos' => 'Di Luar (Menjemur)'],
            ['ldr' => 45, 'rain' => 10, 'weather' => 'Gerimis', 'pos' => 'Di Dalam'],
            ['ldr' => 20, 'rain' => 60, 'weather' => 'Hujan Deras', 'pos' => 'Di Dalam'],
            ['ldr' => 55, 'rain' => 5, 'weather' => 'Gerimis Ringan', 'pos' => 'Di Dalam'],
            ['ldr' => 85, 'rain' => 0, 'weather' => 'Cerah Kembali', 'pos' => 'Di Luar (Menjemur)'],
        ];

        $now = Carbon::now()->subHours(6); // Mulai 6 jam lalu

        foreach ($statuses as $stat) {
            SensorLog::create([
                'ldr_value' => $stat['ldr'],
                'rain_percentage' => $stat['rain'],
                'weather_condition' => $stat['weather'],
                'clothesline_status' => $stat['pos'],
                'created_at' => $now->addHour(),
                'updated_at' => $now
            ]);
        }
    }
}
