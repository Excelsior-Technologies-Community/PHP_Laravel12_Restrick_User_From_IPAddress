<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIpMiddleware
{
    /**
     * List of blocked IP addresses
     * Add any IP you want to restrict
     */
    public $blockIps = [
        'whitelist-ip-1',
        'whitelist-ip-2',
        '127.0.0.1', // Localhost example
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the current user IP exists in blocked IP list
        if (in_array($request->ip(), $this->blockIps)) {
            // Abort request with 403 Forbidden response
            abort(403, 'You are restricted to access the site.');
        }

        // Allow request if IP is not blocked
        return $next($request);
    }
}
