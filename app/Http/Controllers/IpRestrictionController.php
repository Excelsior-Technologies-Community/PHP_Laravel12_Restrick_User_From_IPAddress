<?php

namespace App\Http\Controllers;

use App\Models\BlockedIpLog;
use App\Models\IpRestriction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IpRestrictionController extends Controller
{
    /**
     * Display IP restrictions and blocked logs.
     */
    public function index()
    {
        $restrictions = IpRestriction::latest()->get();

        $logs = BlockedIpLog::latest('blocked_at')
            ->paginate(15);

        return view('ip-restrictions.index', compact(
            'restrictions',
            'logs'
        ));
    }

    /**
     * Store a new IP restriction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => [
                'required',
                'ip',
                'unique:ip_restrictions,ip_address',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:255',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:now',
            ],
        ]);

        IpRestriction::create([
            'ip_address' => $validated['ip_address'],
            'reason' => $validated['reason'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP address has been blocked successfully.');
    }

    /**
     * Remove an IP restriction.
     */
    public function destroy(IpRestriction $ipRestriction)
    {
        $ipRestriction->delete();

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP restriction removed successfully.');
    }

    /**
     * Enable an IP restriction.
     */
    public function activate(IpRestriction $ipRestriction)
    {
        $ipRestriction->update([
            'is_active' => true,
        ]);

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP restriction activated successfully.');
    }

    /**
     * Disable an IP restriction.
     */
    public function deactivate(IpRestriction $ipRestriction)
    {
        $ipRestriction->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP restriction deactivated successfully.');
    }

    /**
     * Clear all blocked IP logs.
     */
    public function clearLogs()
    {
        BlockedIpLog::truncate();

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'Blocked IP logs cleared successfully.');
    }

    public function analytics()
    {
        $totalBlockedAttempts = BlockedIpLog::count();

        $todayBlockedAttempts = BlockedIpLog::whereDate(
            'blocked_at',
            today()
        )->count();

        $lastSevenDaysBlockedAttempts = BlockedIpLog::where(
            'blocked_at',
            '>=',
            now()->subDays(6)->startOfDay()
        )->count();

        $uniqueBlockedIps = BlockedIpLog::distinct(
            'ip_address'
        )->count('ip_address');

        $topBlockedIps = BlockedIpLog::select('ip_address')
            ->selectRaw('COUNT(*) as attempts')
            ->groupBy('ip_address')
            ->orderByDesc('attempts')
            ->limit(10)
            ->get();

        $topTargetedRoutes = BlockedIpLog::select('path')
            ->selectRaw('COUNT(*) as attempts')
            ->whereNotNull('path')
            ->groupBy('path')
            ->orderByDesc('attempts')
            ->limit(10)
            ->get();

        $methodStatistics = BlockedIpLog::select('method')
            ->selectRaw('COUNT(*) as attempts')
            ->whereNotNull('method')
            ->groupBy('method')
            ->orderByDesc('attempts')
            ->get();

        $sevenDayStatistics = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();

            $sevenDayStatistics[] = [
                'date' => $date->format('d M'),
                'attempts' => BlockedIpLog::whereDate(
                    'blocked_at',
                    $date->toDateString()
                )->count(),
            ];
        }

        $recentActivity = BlockedIpLog::latest('blocked_at')
            ->limit(10)
            ->get();

        return view('security-analytics.index', compact(
            'totalBlockedAttempts',
            'todayBlockedAttempts',
            'lastSevenDaysBlockedAttempts',
            'uniqueBlockedIps',
            'topBlockedIps',
            'topTargetedRoutes',
            'methodStatistics',
            'sevenDayStatistics',
            'recentActivity'
        ));
    }
}
