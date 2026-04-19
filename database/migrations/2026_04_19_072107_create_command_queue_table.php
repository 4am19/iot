<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('command_queue', function (Blueprint $table) {
            $table->id();
            // Jenis perintah: 'move_in', 'move_out', 'set_auto', 'set_manual'
            $table->string('command');
            // Payload tambahan opsional, misal: {"ldr_threshold": 40}
            $table->json('payload')->nullable();
            // Apakah perintah sudah dieksekusi oleh ESP32
            $table->boolean('executed')->default(false);
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('command_queue');
    }
};
