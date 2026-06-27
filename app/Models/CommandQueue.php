<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandQueue extends Model
{
    protected $table = 'command_queue';

    protected $fillable = [
        'device_id',
        'command',
        'payload',
        'executed',
        'executed_at',
    ];

    protected $casts = [
        'payload'     => 'array',
        'executed'    => 'boolean',
        'executed_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
