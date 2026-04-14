<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceSetting extends Model
{
    protected $fillable = [
        'is_auto_mode',
        'ldr_threshold',
        'rain_threshold',
        'manual_position',
        'owner_name',
        'device_key'
    ];

    protected $casts = [
        'is_auto_mode' => 'boolean',
        'ldr_threshold' => 'integer',
        'rain_threshold' => 'integer',
    ];
}
