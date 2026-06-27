<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('mac_address')->unique(); // ID unik ESP32
            $table->string('name')->default('Jemuran Cerdas');
            $table->boolean('is_auto_mode')->default(true);
            $table->integer('ldr_threshold')->default(50);
            $table->integer('rain_threshold')->default(5);
            $table->string('manual_position')->default('Di Luar (Menjemur)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
