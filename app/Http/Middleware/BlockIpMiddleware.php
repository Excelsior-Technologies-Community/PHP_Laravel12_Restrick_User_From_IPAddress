<?php

namespace App\Http\Middleware;

use App\Models\BlockedIpLog;
use App\Models\IpRestriction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIpMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $ipAddress = $request->ip();

        $restriction = IpRestriction::where(
            'ip_address',
            $ipAddress
        )
            ->where('is_active', true)
            ->first();

        if ($restriction) {

            /*
            |--------------------------------------------------------------------------
            | Automatically deactivate expired restriction
            |--------------------------------------------------------------------------
            */

            if (
                $restriction->expires_at &&
                $restriction->expires_at->isPast()
            ) {
                $restriction->update([
                    'is_active' => false,
                ]);

                return $next($request);
            }

            /*
            |--------------------------------------------------------------------------
            | Block request
            |--------------------------------------------------------------------------
            */

            if ($restriction->isCurrentlyBlocked()) {

                BlockedIpLog::create([
                    'ip_address' => $ipAddress,
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'user_agent' => $request->userAgent(),
                    'blocked_at' => now(),
                ]);

                abort(
                    403,
                    'You are restricted from accessing this site.'
                );
            }
        }

        return $next($request);
    }
}
