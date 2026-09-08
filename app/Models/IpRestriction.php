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

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function matchesIp(string $ipAddress): bool
    {
        if ($this->restriction_type === 'cidr' && $this->cidr) {
            return $this->ipInCidr($ipAddress, $this->cidr);
        }

        if ($this->restriction_type === 'country' && $this->country_code) {
            $geo = $this->fetchGeolocation($ipAddress);
            return $geo && strtoupper($geo['countryCode']) === strtoupper($this->country_code);
        }

        return $this->ip_address === $ipAddress;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $mask = -1 << (32 - (int) $bits);

        $subnetLong &= $mask;

        return ($ipLong & $mask) === $subnetLong;
    }

    private function fetchGeolocation(string $ipAddress): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)
                ->get("http://ip-api.com/json/{$ipAddress}");

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            // Silently fail geolocation lookup
        }

        return null;
    }
}
