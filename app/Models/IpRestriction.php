<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpRestriction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'reason',
        'expires_at',
        'is_active',
        'restriction_type',
        'cidr',
        'country_code',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isCurrentlyBlocked(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (
            $this->expires_at !== null &&
            $this->expires_at->isPast()
        ) {
            return false;
        }

        return true;
    }
}
