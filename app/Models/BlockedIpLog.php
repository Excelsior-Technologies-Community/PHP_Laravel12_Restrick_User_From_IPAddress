<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIpLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'path',
        'method',
        'user_agent',
        'blocked_at',
    ];

    protected function casts(): array
    {
        return [
            'blocked_at' => 'datetime',
        ];
    }
}