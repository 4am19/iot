<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('ldr_value')->default(0)->comment('Nilai sensor cahaya mentah atau %');
            $table->float('rain_percentage')->default(0)->comment('Persentase curah hujan');
            $table->string('weather_condition')->default('Cerah Terik');
            $table->string('clothesline_status')->default('Di Luar (Menjemur)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_logs');
    }
};
