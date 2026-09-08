<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpWhitelist extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'reason',
    ];

    protected function casts(): array
    {
        return [];
    }
}
