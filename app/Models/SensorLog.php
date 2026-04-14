<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    protected $fillable = [
        'ldr_value',
        'rain_percentage',
        'weather_condition',
        'clothesline_status',
    ];

    protected $casts = [
        'ldr_value' => 'integer',
        'rain_percentage' => 'float',
    ];
}
