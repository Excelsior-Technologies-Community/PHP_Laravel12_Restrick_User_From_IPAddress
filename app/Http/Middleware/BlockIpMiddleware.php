<?php

namespace App\Http\Middleware;

use App\Models\BlockedIpLog;
use App\Models\IpRestriction;
use App\Models\IpWhitelist;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class BlockIpMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();

        if ($this->isWhitelisted($ipAddress)) {
            return $next($request);
        }

        if ($this->isRateLimited($ipAddress)) {
            return $this->block($request, $ipAddress, true);
        }

        $restriction = IpRestriction::where('is_active', true)
            ->get()
            ->first(function ($r) use ($ipAddress) {
                return $r->matchesIp($ipAddress) && $r->isCurrentlyBlocked();
            });

        if ($restriction) {
            return $this->block($request, $ipAddress, $restriction->restriction_type === 'auto');
        }

        $this->trackSuspiciousActivity($ipAddress);

        return $next($request);
    }

    private function isWhitelisted(string $ipAddress): bool
    {
        return IpWhitelist::where('ip_address', $ipAddress)->exists();
    }

    private function isRateLimited(string $ipAddress): bool
    {
        $maxRequests = (int) config('ip-restriction.rate_limit_max_requests', 100);
        $windowSeconds = (int) config('ip-restriction.rate_limit_window_seconds', 60);

        $key = "ip_rate_limit:{$ipAddress}";

        $requests = Cache::get($key, 0);

        if ($requests >= $maxRequests) {
            return true;
        }

        Cache::put($key, $requests + 1, $windowSeconds / 60);

        return false;
    }

    private function trackSuspiciousActivity(string $ipAddress): void
    {
        $key = "suspicious_activity:{$ipAddress}";
        $attempts = Cache::get($key, 0);
        $attempts++;

        Cache::put($key, $attempts, 60);

        $autoBlockThreshold = (int) config('ip-restriction.auto_block_threshold', 5);

        if ($attempts >= $autoBlockThreshold) {
            $existing = IpRestriction::where('ip_address', $ipAddress)
                ->where('restriction_type', 'auto')
                ->first();

            if (!$existing) {
                IpRestriction::create([
                    'ip_address' => $ipAddress,
                    'reason' => 'Auto-blocked due to suspicious activity',
                    'restriction_type' => 'auto',
                    'is_active' => true,
                ]);
            }
        }
    }

    private function block(Request $request, string $ipAddress, bool $isAutoBlocked): Response
    {
        $geo = $this->fetchGeolocation($ipAddress);

        BlockedIpLog::create([
            'ip_address' => $ipAddress,
            'path' => $request->path(),
            'method' => $request->method(),
            'user_agent' => $request->userAgent(),
            'blocked_at' => now(),
            'country_code' => $geo['countryCode'] ?? null,
            'city' => $geo['city'] ?? null,
            'is_auto_blocked' => $isAutoBlocked,
        ]);

        if (config('ip-restriction.send_email_alerts', false)) {
            $this->sendAlertEmail($ipAddress, $request);
        }

        return response()->view('errors.blocked', [
            'ip_address' => $ipAddress,
        ], 403);
    }

    private function fetchGeolocation(string $ipAddress): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)
                ->get("http://ip-api.com/json/{$ipAddress}");

            if ($response->successful() && $response->json('status') === 'success') {
                return $response->json();
            }
        } catch (\Throwable $e) {
            // Silently fail geolocation lookup
        }

        return null;
    }

    private function sendAlertEmail(string $ipAddress, Request $request): void
    {
        try {
            \Illuminate\Support\Facades\Mail::to(config('ip-restriction.alert_email'))
                ->send(new \App\Mail\BlockedIpAlert($ipAddress, $request->path(), $request->method()));
        } catch (\Throwable $e) {
            // Silently fail email sending
        }
    }
}
