<?php

namespace App\Http\Controllers;

use App\Models\BlockedIpLog;
use App\Models\IpRestriction;
use App\Models\IpWhitelist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IpRestrictionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $filterType = $request->query('type');
        $filterStatus = $request->query('status');
        $logSearch = $request->query('log_search');
        $logMethod = $request->query('log_method');
        $logDateFrom = $request->query('date_from');
        $logDateTo = $request->query('date_to');
        $darkMode = $request->cookie('dark_mode', '0');

        $restrictionsQuery = IpRestriction::query();

        if ($search) {
            $restrictionsQuery->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%")
                  ->orWhere('country_code', 'like', "%{$search}%");
            });
        }

        if ($filterType) {
            $restrictionsQuery->where('restriction_type', $filterType);
        }

        if ($filterStatus) {
            if ($filterStatus === 'active') {
                $restrictionsQuery->where('is_active', true);
            } elseif ($filterStatus === 'expired') {
                $restrictionsQuery->where('expires_at', '<', now());
            } elseif ($filterStatus === 'allowed') {
                $restrictionsQuery->where('is_active', false);
            }
        }

        $restrictions = $restrictionsQuery->latest()->get();

        $logsQuery = BlockedIpLog::query();

        if ($logSearch) {
            $logsQuery->where('ip_address', 'like', "%{$logSearch}%")
                ->orWhere('path', 'like', "%{$logSearch}%")
                ->orWhere('user_agent', 'like', "%{$logSearch}%");
        }

        if ($logMethod) {
            $logsQuery->where('method', $logMethod);
        }

        if ($logDateFrom) {
            $logsQuery->whereDate('blocked_at', '>=', $logDateFrom);
        }

        if ($logDateTo) {
            $logsQuery->whereDate('blocked_at', '<=', $logDateTo);
        }

        $logs = $logsQuery->latest('blocked_at')->paginate(15)->appends($request->query());

        return view('ip-restrictions.index', compact(
            'restrictions',
            'logs',
            'search',
            'filterType',
            'filterStatus',
            'logSearch',
            'logMethod',
            'logDateFrom',
            'logDateTo',
            'darkMode'
        ));
    }

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
            ->with('success', "Successfully imported {$count} IP restriction(s).");
    }

    public function export(Request $request, string $format)
    {
        $restrictions = IpRestriction::latest()->get();

        if ($format === 'csv') {
            $filename = 'ip_restrictions_' . now()->format('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($restrictions) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['IP Address', 'Type', 'CIDR', 'Country', 'Reason', 'Expires At', 'Status']);
                foreach ($restrictions as $r) {
                    fputcsv($file, [
                        $r->ip_address,
                        $r->restriction_type,
                        $r->cidr ?? '',
                        $r->country_code ?? '',
                        $r->reason ?? '',
                        $r->expires_at?->format('Y-m-d H:i:s') ?? 'Permanent',
                        $r->isCurrentlyBlocked() ? 'Active' : 'Inactive',
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        if ($format === 'json') {
            $filename = 'ip_restrictions_' . now()->format('Y-m-d_H-i-s') . '.json';
            $data = $restrictions->map(fn ($r) => $r->only([
                'ip_address', 'restriction_type', 'cidr', 'country_code',
                'reason', 'expires_at', 'is_active',
            ]));

            return response()->json($data)->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
        }

        return redirect()->route('ip-restrictions.index');
    }

    public function exportLogs()
    {
        $logs = BlockedIpLog::latest('blocked_at')->get();

        $filename = 'blocked_ip_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['IP Address', 'Country', 'City', 'Path', 'Method', 'User Agent', 'Blocked At', 'Auto Blocked']);
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->ip_address,
                    $log->country_code ?? '',
                    $log->city ?? '',
                    $log->path ?? '',
                    $log->method ?? '',
                    $log->user_agent ?? '',
                    $log->blocked_at->format('Y-m-d H:i:s'),
                    $log->is_auto_blocked ? 'Yes' : 'No',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(IpRestriction $ipRestriction)
    {
        $ipRestriction->delete();

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP restriction removed successfully.');
    }

    public function activate(IpRestriction $ipRestriction)
    {
        $ipRestriction->update(['is_active' => true]);

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP restriction activated successfully.');
    }

    public function deactivate(IpRestriction $ipRestriction)
    {
        $ipRestriction->update(['is_active' => false]);

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'IP restriction deactivated successfully.');
    }

    public function clearLogs()
    {
        BlockedIpLog::truncate();

        return redirect()
            ->route('ip-restrictions.index')
            ->with('success', 'Blocked IP logs cleared successfully.');
    }

    public function toggleDarkMode(Request $request)
    {
        $current = $request->cookie('dark_mode', '0');
        $newValue = $current === '1' ? '0' : '1';

        return redirect()
            ->back()
            ->withCookie(cookie('dark_mode', $newValue, 60 * 24 * 30));
    }

    public function analytics(Request $request)
    {
        $search = $request->query('search');
        $methodFilter = $request->query('method');

        $totalBlockedAttempts = BlockedIpLog::count();
        $todayBlockedAttempts = BlockedIpLog::whereDate('blocked_at', today())->count();
        $lastSevenDaysBlockedAttempts = BlockedIpLog::where('blocked_at', '>=', now()->subDays(6)->startOfDay())->count();
        $uniqueBlockedIps = BlockedIpLog::distinct('ip_address')->count('ip_address');

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

        $methodQuery = BlockedIpLog::select('method')
            ->selectRaw('COUNT(*) as attempts')
            ->whereNotNull('method')
            ->groupBy('method')
            ->orderByDesc('attempts');

        if ($methodFilter) {
            $methodQuery->where('method', $methodFilter);
        }

        $methodStatistics = $methodQuery->get();

        $sevenDayStatistics = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $sevenDayStatistics[] = [
                'date' => $date->format('d M'),
                'attempts' => BlockedIpLog::whereDate('blocked_at', $date->toDateString())->count(),
            ];
        }

        $recentActivityQuery = BlockedIpLog::latest('blocked_at')->limit(10);

        if ($search) {
            $recentActivityQuery->where('ip_address', 'like', "%{$search}%");
        }

        $recentActivity = $recentActivityQuery->get();

        return view('security-analytics.index', compact(
            'totalBlockedAttempts',
            'todayBlockedAttempts',
            'lastSevenDaysBlockedAttempts',
            'uniqueBlockedIps',
            'topBlockedIps',
            'topTargetedRoutes',
            'methodStatistics',
            'sevenDayStatistics',
            'recentActivity',
            'search',
            'methodFilter',
        ));
    }
}
