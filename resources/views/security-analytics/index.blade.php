<!DOCTYPE html>
<html lang="en" data-theme="{{ request()->cookie('dark_mode', '0') == '1' ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-body: #f5f7fb;
            --bg-card: #ffffff;
            --bg-input: #ffffff;
            --text-color: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --table-hover: rgba(0, 0, 0, 0.02);
            --shadow-color: rgba(0, 0, 0, 0.06);
            --badge-bg: #6c757d;
            --badge-color: #ffffff;
            --thead-bg: #f8f9fa;
            --thead-color: #212529;
            --link-color: #0d6efd;
        }
        [data-theme="dark"] {
            --bg-body: #0b0d10;
            --bg-card: #15181e;
            --bg-input: #1c1f27;
            --text-color: #e4e6eb;
            --text-muted: #9aa3af;
            --border-color: #2a2f3a;
            --table-hover: rgba(255, 255, 255, 0.03);
            --shadow-color: rgba(0, 0, 0, 0.45);
            --badge-bg: #2a2f3a;
            --badge-color: #e4e6eb;
            --thead-bg: #1c1f27;
            --thead-color: #e4e6eb;
            --link-color: #7aa6ff;
        }
        body {
            background-color: var(--bg-body);
            color: var(--text-color);
            transition: background 0.3s, color 0.3s;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #212529, #343a40);
            color: white;
            border-radius: 18px;
            padding: 30px;
            margin-bottom: 25px;
        }
        [data-theme="dark"] .dashboard-header {
            background: linear-gradient(135deg, #0b0d10, #1f232b);
        }
        .stat-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px var(--shadow-color);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .analytics-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 3px 15px var(--shadow-color);
            background: var(--bg-card);
            transition: background 0.3s;
            border: 1px solid var(--border-color);
        }
        .analytics-card .card-header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 18px 20px;
            border-radius: 16px 16px 0 0;
            color: var(--text-color);
        }
        [data-theme="dark"] .analytics-card .card-header {
            border-bottom-color: #2a2f3a;
        }
        .chart-container {
            position: relative;
            height: 320px;
        }
        .ip-badge {
            font-family: monospace;
            font-size: 14px;
        }
        .route-text {
            font-family: monospace;
            word-break: break-all;
        }
        .progress {
            height: 8px;
            border-radius: 10px;
            background-color: var(--border-color);
        }
        .progress-bar {
            background-color: #7aa6ff;
        }
        [data-theme="dark"] .progress-bar {
            background-color: #4d7cff;
        }
        .activity-row td {
            vertical-align: middle;
        }
        .empty-state {
            padding: 35px 15px;
            text-align: center;
            color: var(--text-muted);
        }
        .method-badge {
            min-width: 70px;
            display: inline-block;
            text-align: center;
        }
        .form-control, .form-select {
            background: var(--bg-input);
            color: var(--text-color);
            border-color: var(--border-color);
        }
        .form-control:focus, .form-select:focus {
            background: var(--bg-input);
            color: var(--text-color);
            border-color: #7aa6ff;
        }
        .form-control::placeholder {
            color: var(--text-muted);
        }
        .form-label {
            color: var(--text-color);
            font-weight: 500;
        }
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text-color);
        }
        .table thead th {
            background: var(--thead-bg);
            color: var(--thead-color);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
        }
        .table-hover tbody tr:hover {
            --bs-table-bg: var(--table-hover);
        }
        .small, small {
            color: var(--text-muted);
        }
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-color);
        }
        a {
            color: var(--link-color);
        }
        a:hover {
            filter: brightness(1.1);
        }
        .btn-close {
            filter: invert(1);
        }
        [data-theme="light"] .btn-close {
            filter: invert(0);
        }
        .table-light {
            background-color: var(--thead-bg);
            color: var(--thead-color);
        }
        [data-theme="dark"] .table-light {
            background-color: #1c1f27;
        }
        .pagination {
            --bs-pagination-color: var(--text-color);
            --bs-pagination-bg: var(--bg-card);
            --bs-pagination-border-color: var(--border-color);
        }
        .pagination .page-link {
            background: var(--bg-card);
            color: var(--text-color);
            border-color: var(--border-color);
        }
        .pagination .page-item.active .page-link {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        .pagination .page-item.disabled .page-link {
            background: var(--bg-card);
            color: var(--text-muted);
            border-color: var(--border-color);
        }
        .badge {
            background: var(--badge-bg);
            color: var(--badge-color);
        }
        .badge.bg-danger {
            background: #dc3545 !important;
            color: #fff !important;
        }
        .badge.bg-success {
            background: #198754 !important;
            color: #fff !important;
        }
        .badge.bg-warning {
            background: #ffc107 !important;
            color: #000 !important;
        }
        .badge.bg-info {
            background: #0dcaf0 !important;
            color: #000 !important;
        }
        .badge.bg-secondary {
            background: #6c757d !important;
            color: #fff !important;
        }
        [data-theme="dark"] .badge.bg-light {
            background: #2a2f3a !important;
            color: #e4e6eb !important;
            border: 1px solid #3a4250;
        }
        [data-theme="dark"] .badge.bg-dark {
            background: #0d1117 !important;
            color: #c9d1d9 !important;
            border: 1px solid #30363d;
        }
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-color);
        }
        p {
            color: var(--text-color);
        }
        .card-body {
            color: var(--text-color);
        }
        .card-body p, .card-body small, .card-body span {
            color: var(--text-color);
        }
        .card-body .text-muted, .card-body small.text-muted {
            color: var(--text-muted) !important;
        }
        .table tbody td {
            color: var(--text-color);
        }
    </style>
</head>
<body>
<div class="container-fluid px-3 px-md-4 py-4">
    {{-- Header --}}
    <div class="dashboard-header">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h2 fw-bold mb-2">📊 Security Analytics</h1>
                <p class="mb-0 text-white-50">Monitor blocked IP activity, security events, targeted routes, and access patterns.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('ip-restrictions.index') }}" class="btn btn-light">← IP Management</a>
                <form action="{{ route('ip-restrictions.dark-mode.toggle') }}" method="GET">
                    <button type="submit" class="btn btn-outline-light">
                        {{ request()->cookie('dark_mode', '0') == '1' ? '☀️ Light' : '🌙 Dark' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Total Blocked Attempts</p>
                            <h2 class="fw-bold mb-0">{{ number_format($totalBlockedAttempts) }}</h2>
                        </div>
                        <div class="stat-icon bg-danger-subtle text-danger">🛡️</div>
                    </div>
                    <small class="text-muted">All recorded blocked requests</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Blocked Today</p>
                            <h2 class="fw-bold mb-0">{{ number_format($todayBlockedAttempts) }}</h2>
                        </div>
                        <div class="stat-icon bg-warning-subtle text-warning">🚨</div>
                    </div>
                    <small class="text-muted">Security events recorded today</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Last 7 Days</p>
                            <h2 class="fw-bold mb-0">{{ number_format($lastSevenDaysBlockedAttempts) }}</h2>
                        </div>
                        <div class="stat-icon bg-primary-subtle text-primary">📈</div>
                    </div>
                    <small class="text-muted">Recent blocked access attempts</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card h-100 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2">Unique Blocked IPs</p>
                            <h2 class="fw-bold mb-0">{{ number_format($uniqueBlockedIps) }}</h2>
                        </div>
                        <div class="stat-icon bg-success-subtle text-success">🌐</div>
                    </div>
                    <small class="text-muted">Different IP addresses detected</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Seven Day Chart --}}
    <div class="card analytics-card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1 fw-bold">📈 Blocked Attempts - Last 7 Days</h5>
                    <small class="text-muted">Daily blocked request activity</small>
                </div>
                <span class="badge bg-dark">7 Days</span>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="blockedAttemptsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card analytics-card mb-4">
        <div class="card-body p-3">
            <form action="{{ route('security-analytics.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search IP / Route</label>
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Method</label>
                    <select name="method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="GET" {{ ($methodFilter ?? '') == 'GET' ? 'selected' : '' }}>GET</option>
                        <option value="POST" {{ ($methodFilter ?? '') == 'POST' ? 'selected' : '' }}>POST</option>
                        <option value="PUT" {{ ($methodFilter ?? '') == 'PUT' ? 'selected' : '' }}>PUT</option>
                        <option value="DELETE" {{ ($methodFilter ?? '') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Top Blocked IPs --}}
        <div class="col-12 col-xl-6">
            <div class="card analytics-card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-1">🌐 Top Blocked IP Addresses</h5>
                    <small class="text-muted">IP addresses generating the most blocked requests</small>
                </div>
                <div class="card-body">
                    @if($topBlockedIps->count())
                        @php $maxIpAttempts = $topBlockedIps->max('attempts'); @endphp
                        @foreach($topBlockedIps as $index => $ip)
                            @php $percentage = $maxIpAttempts > 0 ? ($ip->attempts / $maxIpAttempts) * 100 : 0; @endphp
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="badge bg-light text-dark border me-2">#{{ $index + 1 }}</span>
                                        <span class="ip-badge">{{ $ip->ip_address }}</span>
                                    </div>
                                    <strong>{{ number_format($ip->attempts) }}</strong>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="fs-1 mb-2">🌐</div>
                            <p class="mb-0">No blocked IP activity recorded yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- HTTP Methods --}}
        <div class="col-12 col-xl-6">
            <div class="card analytics-card h-100">
                <div class="card-header">
                    <h5 class="fw-bold mb-1">🔐 Blocked Requests by Method</h5>
                    <small class="text-muted">HTTP methods associated with blocked requests</small>
                </div>
                <div class="card-body">
                    @if($methodStatistics->count())
                        @php $maxMethodAttempts = $methodStatistics->max('attempts'); @endphp
                        @foreach($methodStatistics as $method)
                            @php $percentage = $maxMethodAttempts > 0 ? ($method->attempts / $maxMethodAttempts) * 100 : 0; @endphp
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-dark method-badge">{{ $method->method }}</span>
                                    <strong>{{ number_format($method->attempts) }}</strong>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <div class="fs-1 mb-2">🔐</div>
                            <p class="mb-0">No HTTP method statistics available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Targeted Routes --}}
    <div class="card analytics-card mb-4">
        <div class="card-header">
            <h5 class="fw-bold mb-1">🎯 Most Targeted Routes</h5>
            <small class="text-muted">Routes receiving the highest number of blocked requests</small>
        </div>
        <div class="card-body p-0">
            @if($topTargetedRoutes->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">#</th>
                                <th>Route</th>
                                <th class="text-end px-4">Blocked Attempts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topTargetedRoutes as $index => $route)
                                <tr>
                                    <td class="px-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td><span class="route-text">/{{ ltrim($route->path, '/') }}</span></td>
                                    <td class="text-end px-4">
                                        <span class="badge bg-danger">{{ number_format($route->attempts) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="fs-1 mb-2">🎯</div>
                    <p class="mb-0">No targeted route data available.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="card analytics-card mb-4">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="fw-bold mb-1">🚨 Recent Security Activity</h5>
                    <small class="text-muted">Latest blocked access attempts</small>
                </div>
                <a href="{{ route('ip-restrictions.index') }}" class="btn btn-sm btn-outline-light">View All Logs</a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($recentActivity->count())
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">IP Address</th>
                                <th>Country</th>
                                <th>Method</th>
                                <th>Path</th>
                                <th>User Agent</th>
                                <th class="text-end px-4">Blocked At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivity as $activity)
                                <tr class="activity-row">
                                    <td class="px-4">
                                        <span class="badge bg-danger-subtle text-danger ip-badge">{{ $activity->ip_address }}</span>
                                    </td>
                                    <td>
                                        @if($activity->country_code)
                                        <span class="badge bg-info">{{ $activity->country_code }}</span>
                                        @if($activity->city)
                                        <small class="text-muted d-block">{{ $activity->city }}</small>
                                        @endif
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-dark">{{ $activity->method ?? 'N/A' }}</span></td>
                                    <td><span class="route-text">/{{ ltrim($activity->path ?? '', '/') }}</span></td>
                                    <td>
                                        <span class="text-muted d-inline-block" style="max-width: 280px;" title="{{ $activity->user_agent }}">
                                            {{ $activity->user_agent ? \Illuminate\Support\Str::limit($activity->user_agent, 45) : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <span class="text-muted">{{ $activity->blocked_at?->format('d M Y, h:i A') ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="fs-1 mb-2">🛡️</div>
                    <h6 class="fw-bold">No Security Activity</h6>
                    <p class="mb-0">No blocked access attempts have been recorded yet.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center text-muted py-3">
        <small>IP Restriction Security Analytics &copy; {{ date('Y') }}</small>
    </div>
</div>

<script>
    const statistics = @json($sevenDayStatistics);
    const labels = statistics.map(item => item.date);
    const attempts = statistics.map(item => item.attempts);
    const chartElement = document.getElementById('blockedAttemptsChart');

    if (chartElement) {
        new Chart(chartElement, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Blocked Attempts',
                    data: attempts,
                    borderWidth: 3,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: { legend: { display: true } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
