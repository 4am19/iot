<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'mac_address',
        'name',
        'is_auto_mode',
        'ldr_threshold',
        'rain_threshold',
        'manual_position',
    ];

    protected $casts = [
        'is_auto_mode' => 'boolean',
        'ldr_threshold' => 'integer',
        'rain_threshold' => 'integer',
    ];

    /**
     * Users yang memiliki akses ke device ini (Master atau Member)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Sensor logs dari device ini
     */
    public function logs(): HasMany
    {
        return $this->hasMany(SensorLog::class);
    }
}
