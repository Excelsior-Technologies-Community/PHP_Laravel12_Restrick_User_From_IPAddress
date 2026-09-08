<?php

namespace App\Http\Controllers;

use App\Models\BlockedIpLog;
use App\Models\IpRestriction;
use App\Models\IpWhitelist;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class IpRestrictionController extends Controller
{
    /**
     * Display IP restrictions and blocked logs.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = $request->input('search');

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $status = $request->input('status', 'all');

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        $sort = $request->input('sort', 'latest');

        /*
        |--------------------------------------------------------------------------
        | Restrictions Query
        |--------------------------------------------------------------------------
        */

        $restrictionQuery = IpRestriction::query();

        if ($search) {
            $restrictionQuery->where(function ($query) use ($search) {
                $query->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($status === 'active') {

            $restrictionQuery
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                });
        } elseif ($status === 'disabled') {

            $restrictionQuery->where('is_active', false);
        } elseif ($status === 'expired') {

            $restrictionQuery
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', now());
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */

        switch ($sort) {

            case 'oldest':
                $restrictionQuery->oldest();
                break;

            case 'ip_asc':
                $restrictionQuery->orderBy('ip_address', 'asc');
                break;

            case 'ip_desc':
                $restrictionQuery->orderBy('ip_address', 'desc');
                break;

            default:
                $restrictionQuery->latest();
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $restrictions = $restrictionQuery
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Logs
        |--------------------------------------------------------------------------
        */

        $logs = BlockedIpLog::latest('blocked_at')
            ->paginate(5, ['*'], 'logs_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalRestrictions = IpRestriction::count();

        $activeRestrictions = IpRestriction::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->count();

        $expiredRestrictions = IpRestriction::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->count();

        $blockedAttempts = BlockedIpLog::count();

        return view(
            'ip-restrictions.index',
            compact(
                'restrictions',
                'logs',
                'search',
                'status',
                'sort',
                'totalRestrictions',
                'activeRestrictions',
                'expiredRestrictions',
                'blockedAttempts'
            )
        );
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
                Rule::unique('ip_restrictions', 'ip_address'),
                Rule::unique('ip_whitelists', 'ip_address'),
            ],
            'restriction_type' => ['required', 'in:single,cidr,country,whitelist'],
            'reason' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'cidr' => ['nullable', 'regex:/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/'],
            'country_code' => ['nullable', 'size:2'],
        ]);

        $type = $validated['restriction_type'];

        if ($type === 'whitelist') {
            IpWhitelist::create([
                'ip_address' => $validated['ip_address'],
                'reason' => $validated['reason'] ?? null,
            ]);

            return redirect()
                ->route('ip-restrictions.index')
                ->with('success', 'IP address has been whitelisted successfully.');
        }

        $data = [
            'ip_address' => $validated['ip_address'],
            'reason' => $validated['reason'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
            'restriction_type' => $type,
        ];

        if ($type === 'cidr') {
            $data['cidr'] = $validated['cidr'];
        } elseif ($type === 'country') {
            $data['country_code'] = strtoupper($validated['country_code']);
        }

        IpRestriction::create($data);

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP restriction has been added successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'in:activate,deactivate,delete'],
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:ip_restrictions,id'],
        ]);

        $ids = $request->input('ids', []);

        switch ($request->input('action')) {
            case 'activate':
                IpRestriction::whereIn('id', $ids)->update(['is_active' => true]);
                $message = count($ids) . ' restriction(s) activated successfully.';
                break;
            case 'deactivate':
                IpRestriction::whereIn('id', $ids)->update(['is_active' => false]);
                $message = count($ids) . ' restriction(s) deactivated successfully.';
                break;
            case 'delete':
                IpRestriction::whereIn('id', $ids)->delete();
                $message = count($ids) . ' restriction(s) deleted successfully.';
                break;
        }

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', $message);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => ['required', 'file', 'mimes:csv,txt'],
            'restriction_type' => ['required', 'in:single,cidr,country'],
            'reason' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'cidr' => ['nullable', 'regex:/^(\d{1,3}\.){3}\d{1,3}\/\d{1,2}$/'],
            'country_code' => ['nullable', 'size:2'],
        ]);

        $file = fopen($request->file('import_file')->getRealPath(), 'r');
        $header = fgetcsv($file);
        $count = 0;

        $type = $request->input('restriction_type');
        $reason = $request->input('reason');
        $expiresAt = $request->input('expires_at');
        $cidr = $request->input('cidr');
        $countryCode = $request->input('country_code');

        while (($row = fgetcsv($file)) !== false) {
            $ip = trim($row[0] ?? '');

            if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
                continue;
            }

            if (IpRestriction::where('ip_address', $ip)->exists()
                || IpWhitelist::where('ip_address', $ip)->exists()
            ) {
                continue;
            }

            $data = [
                'ip_address' => $ip,
                'reason' => $reason,
                'expires_at' => $expiresAt,
                'is_active' => true,
                'restriction_type' => $type,
            ];

            if ($type === 'cidr') {
                $data['cidr'] = $cidr;
            } elseif ($type === 'country') {
                $data['country_code'] = strtoupper($countryCode);
            }

            IpRestriction::create($data);
            $count++;
        }

        fclose($file);

        return redirect()
            ->route('ip-restrictions.index')
            ->with(
                'success',
                'IP address has been blocked successfully.'
            );
    }


    /**
     * Remove an IP restriction.
     */
    public function destroy(IpRestriction $ipRestriction)
    {
        $ipRestriction->delete();

        return redirect()
            ->route('ip-restrictions.index')
            ->with(
                'success',
                'IP restriction removed successfully.'
            );
    }


    /**
     * Enable an IP restriction.
     */
    public function activate(IpRestriction $ipRestriction)
    {
        $ipRestriction->update(['is_active' => true]);

        return redirect()
            ->route('ip-restrictions.index')
            ->with(
                'success',
                'IP restriction activated successfully.'
            );
    }


    /**
     * Disable an IP restriction.
     */
    public function deactivate(IpRestriction $ipRestriction)
    {
        $ipRestriction->update(['is_active' => false]);

        return redirect()
            ->route('ip-restrictions.index')
            ->with(
                'success',
                'IP restriction deactivated successfully.'
            );
    }


    /**
     * Clear all blocked IP logs.
     */
    public function clearLogs()
    {
        BlockedIpLog::truncate();

        return redirect()
            ->route('ip-restrictions.index')
            ->with(
                'success',
                'Blocked IP logs cleared successfully.'
            );
    }


    /**
     * Export IP restrictions CSV.
     */
    public function exportRestrictions(): StreamedResponse
    {
        $restrictions = IpRestriction::latest()->get();

        return response()->streamDownload(function () use ($restrictions) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'IP Address',
                'Reason',
                'Status',
                'Expires At',
                'Created At',
            ]);

            foreach ($restrictions as $restriction) {

                if ($restriction->isCurrentlyBlocked()) {
                    $status = 'Blocked';
                } elseif (
                    $restriction->is_active &&
                    $restriction->expires_at &&
                    $restriction->expires_at->isPast()
                ) {
                    $status = 'Expired';
                } else {
                    $status = 'Allowed';
                }

                fputcsv($handle, [
                    $restriction->id,
                    $restriction->ip_address,
                    $restriction->reason ?? '',
                    $status,
                    $restriction->expires_at
                        ? $restriction->expires_at->format(
                            'Y-m-d H:i:s'
                        )
                        : 'Permanent',
                    $restriction->created_at
                        ? $restriction->created_at->format(
                            'Y-m-d H:i:s'
                        )
                        : '',
                ]);
            }

            fclose($handle);
        }, 'ip-restrictions.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }


    /**
     * Export blocked logs CSV.
     */
    public function exportLogs(): StreamedResponse
    {
        $logs = BlockedIpLog::latest('blocked_at')->get();

        return response()->streamDownload(function () use ($logs) {

            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID',
                'IP Address',
                'Method',
                'Path',
                'User Agent',
                'Blocked At',
            ]);

            foreach ($logs as $log) {

                fputcsv($handle, [
                    $log->id,
                    $log->ip_address,
                    $log->method ?? '',
                    $log->path ?? '',
                    $log->user_agent ?? '',
                    $log->blocked_at
                        ? $log->blocked_at->format(
                            'Y-m-d H:i:s'
                        )
                        : '',
                ]);
            }

            fclose($handle);
        }, 'blocked-ip-logs.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }


    /**
     * Security analytics.
     */
    public function analytics(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        $from = $request->input('from');
        $to = $request->input('to');

        $logQuery = BlockedIpLog::query();

        if ($from) {
            $logQuery->whereDate('blocked_at', '>=', $from);
        }

        if ($to) {
            $logQuery->whereDate('blocked_at', '<=', $to);
        }

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $totalBlockedAttempts = (clone $logQuery)->count();

        $todayBlockedAttempts = (clone $logQuery)
            ->whereDate('blocked_at', today())
            ->count();

        $lastSevenDaysBlockedAttempts = (clone $logQuery)
            ->where(
                'blocked_at',
                '>=',
                now()->subDays(6)->startOfDay()
            )
            ->count();

        $uniqueBlockedIps = (clone $logQuery)
            ->distinct('ip_address')
            ->count('ip_address');

        /*
        |--------------------------------------------------------------------------
        | Top Blocked IPs
        |--------------------------------------------------------------------------
        */

        $topBlockedIps = (clone $logQuery)
            ->select('ip_address')
            ->selectRaw('COUNT(*) as attempts')
            ->groupBy('ip_address')
            ->orderByDesc('attempts')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Top Routes
        |--------------------------------------------------------------------------
        */

        $topTargetedRoutes = (clone $logQuery)
            ->select('path')
            ->selectRaw('COUNT(*) as attempts')
            ->whereNotNull('path')
            ->groupBy('path')
            ->orderByDesc('attempts')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | HTTP Methods
        |--------------------------------------------------------------------------
        */

        $methodStatistics = (clone $logQuery)
            ->select('method')
            ->selectRaw('COUNT(*) as attempts')
            ->whereNotNull('method')
            ->groupBy('method')
            ->orderByDesc('attempts')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Seven Day Statistics
        |--------------------------------------------------------------------------
        */

        $sevenDayStatistics = [];
        for ($i = 6; $i >= 0; $i--) {

            $date = now()
                ->subDays($i)
                ->startOfDay();

            $sevenDayStatistics[] = [
                'date' => $date->format('d M'),

                'attempts' => (clone $logQuery)
                    ->whereDate(
                        'blocked_at',
                        $date->toDateString()
                    )
                    ->count(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Recent Activity
        |--------------------------------------------------------------------------
        */

        $recentActivity = (clone $logQuery)
            ->oldest('blocked_at')
            ->limit(5)
            ->get();

        return view(
            'security-analytics.index',
            compact(
                'totalBlockedAttempts',
                'todayBlockedAttempts',
                'lastSevenDaysBlockedAttempts',
                'uniqueBlockedIps',
                'topBlockedIps',
                'topTargetedRoutes',
                'methodStatistics',
                'sevenDayStatistics',
                'recentActivity',
                'from',
                'to'
            )
        );
    }
}
