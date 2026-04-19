<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandQueue extends Model
{
    protected $table = 'command_queue';

    protected $fillable = [
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
}
