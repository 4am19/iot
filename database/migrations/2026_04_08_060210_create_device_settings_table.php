<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_auto_mode')->default(true);
            $table->integer('ldr_threshold')->default(50); // jika ldr < 50, berarti mendung/gelap
            $table->integer('rain_threshold')->default(5); // % air terdeteksi sebelum bergerak
            $table->string('manual_position')->default('Di Luar (Menjemur)');
            $table->string('owner_name')->default('Administrator');
            $table->string('device_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_settings');
    }
};
