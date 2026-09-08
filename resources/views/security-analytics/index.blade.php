<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Security Analytics</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

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
            background: #f5f7fb;
        }

        .analytics-header {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 22px;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-title {
            color: #6c757d;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: 700;
        }
        .analytics-card {
            background: #ffffff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .analytics-card .card-header {
            background: #ffffff;
            border-bottom: 1px solid #eee;
            padding: 18px 20px;
            font-weight: 700;
        }

        .table th {
            white-space: nowrap;
        }

        .badge-ip {
            font-family: monospace;
            font-size: 13px;
        }
        .chart-container {
            position: relative;
            height: 350px;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
        }

        .activity-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }
        .route-text {
            word-break: break-all;
            font-family: monospace;
            font-size: 13px;
        }

        .user-agent {
            max-width: 300px;
            word-break: break-word;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="analytics-header">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="mb-1">🔐 Security Analytics</h2>

                <p class="text-muted mb-0">
                    Laravel IP Restriction Security Monitor
                </p>
            </div>

            <div>
                <a
                    href="{{ route('ip-restrictions.index') }}"
                    class="btn btn-dark">
                    ← IP Restrictions
                </a>
            </div>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- DATE FILTER --}}
    {{-- ========================================================= --}}

    <div class="card analytics-card mb-4">

        <div class="card-body">

            <form
                method="GET"
                action="{{ route('security-analytics.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="from"
                            class="form-control"
                            value="{{ $from ?? '' }}">

                    </div>


                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="to"
                            class="form-control"
                            value="{{ $to ?? '' }}">

                    </div>


                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-dark w-100">
                            🔎 Filter
                        </button>

                    </div>


                    <div class="col-md-2">

                        <a
                            href="{{ route('security-analytics.index') }}"
                            class="btn btn-outline-secondary w-100">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-title">
                    Total Blocked Attempts
                </div>

                <div class="stat-number">
                    {{ number_format($totalBlockedAttempts ?? 0) }}
                </div>

                <small class="text-muted">
                    Blocked requests
                </small>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-title">
                    Today's Blocked Attempts
                </div>

                <div class="stat-number">
                    {{ number_format($todayBlockedAttempts ?? 0) }}
                </div>

                <small class="text-muted">
                    Requests blocked today
                </small>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-title">
                    Last 7 Days
                </div>

                <div class="stat-number">
                    {{ number_format($lastSevenDaysBlockedAttempts ?? 0) }}
                </div>

                <small class="text-muted">
                    Recent blocked requests
                </small>

            </div>

        </div>


        <div class="col-md-6 col-xl-3">

            <div class="stat-card">

                <div class="stat-title">
                    Unique Blocked IPs
                </div>

                <div class="stat-number">
                    {{ number_format($uniqueBlockedIps ?? 0) }}
                </div>

                <small class="text-muted">
                    Different IP addresses
                </small>

            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- 7 DAY CHART --}}
    {{-- ========================================================= --}}

    <div class="card analytics-card mb-4">
        <div class="card-header">
            📊 Blocked Attempts - Last 7 Days
        </div>
        <div class="card-body">

            <div class="chart-container">
                <canvas id="sevenDayChart"></canvas>
            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- TOP BLOCKED IPS + HTTP METHODS --}}
    {{-- ========================================================= --}}

    <div class="row g-4 mb-4">

        {{-- TOP BLOCKED IPS --}}
        <div class="col-lg-6">

            <div class="card analytics-card h-100">
                <div class="card-header">
                    🚫 Top Blocked IP Addresses
                </div>

                <div class="card-body p-0">

                    @if(isset($topBlockedIps) && $topBlockedIps->count())

                        <div class="table-responsive">

                            <table class="table table-hover mb-0">

                                <thead class="table-light">

                                <tr>
                                    <th>#</th>
                                    <th>IP Address</th>
                                    <th class="text-end">
                                        Attempts
                                    </th>
                                </tr>

                                </thead>

                                <tbody>

                                @foreach($topBlockedIps as $index => $item)

                                    <tr>

                                        <td>
                                            {{ $index + 1 }}
                                        </td>

                                        <td>

                                            <span class="badge bg-danger badge-ip">
                                                {{ $item->ip_address }}
                                            </span>

                                        </td>

                                        <td class="text-end fw-bold">
                                            {{ number_format($item->total) }}
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else
                        <div class="empty-state">
                            No blocked IP data available.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- HTTP METHODS --}}
        <div class="col-lg-6">

            <div class="card analytics-card h-100">
                <div class="card-header">
                    🌐 HTTP Methods
                </div>

                <div class="card-body p-0">

                    @if(isset($methodStatistics) && $methodStatistics->count())

                        <div class="table-responsive">

                            <table class="table table-hover mb-0">

                                <thead class="table-light">

                                <tr>
                                    <th>#</th>
                                    <th>Method</th>
                                    <th class="text-end">
                                        Attempts
                                    </th>
                                </tr>

                                </thead>

                                <tbody>

                                @foreach($methodStatistics as $index => $item)

                                    @php
                                        $method = strtoupper($item->method ?? '');

                                        if ($method === 'GET') {
                                            $methodClass = 'bg-primary';
                                        } elseif ($method === 'POST') {
                                            $methodClass = 'bg-success';
                                        } elseif ($method === 'PUT') {
                                            $methodClass = 'bg-warning text-dark';
                                        } elseif ($method === 'PATCH') {
                                            $methodClass = 'bg-info text-dark';
                                        } elseif ($method === 'DELETE') {
                                            $methodClass = 'bg-danger';
                                        } else {
                                            $methodClass = 'bg-secondary';
                                        }
                                    @endphp

                                    <tr>

                                        <td>
                                            {{ $index + 1 }}
                                        </td>

                                        <td>

                                            <span class="badge {{ $methodClass }}">
                                                {{ $method }}
                                            </span>

                                        </td>

                                        <td class="text-end fw-bold">
                                            {{ number_format($item->total) }}
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    @else
                        <div class="empty-state">
                            No HTTP method data available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- TARGETED ROUTES --}}
    {{-- ========================================================= --}}

    <div class="card analytics-card mb-4">
        <div class="card-header">
            🎯 Most Targeted Routes
        </div>
        <div class="card-body p-0">

            @if(isset($topTargetedRoutes) && $topTargetedRoutes->count())

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>
                                Route
                            </th>

                            <th>
                                Attempts
                            </th>

                        </tr>

                        </thead>
                        <tbody>

                        @foreach($topTargetedRoutes as $index => $item)

                            <tr>

                                <td>
                                    {{ $index + 1 }}
                                </td>

                                <td>

                                    <span class="route-text">
                                        {{ $item->path }}
                                    </span>

                                </td>

                                <td class="fw-bold">
                                    {{ number_format($item->total) }}
                                </td>

                            </tr>

                        @endforeach

                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    No targeted route data available.
                </div>
            @endif
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- RECENT ACTIVITY --}}
    {{-- ========================================================= --}}

    <div class="card analytics-card mb-4">
        <div class="card-header">
            🕒 Recent Blocked Activity
        </div>
        <div class="card-body p-0">

            @if(isset($recentActivity) && $recentActivity->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                        <tr>

                            <th>IP Address</th>

                            <th>Method</th>

                            <th>Path</th>

                            <th>User Agent</th>

                            <th>Blocked At</th>

                        </tr>

                        </thead>
                        <tbody>

                        @foreach($recentActivity as $activity)

                            <tr>

                                <td>

                                    <span class="badge bg-danger badge-ip">
                                        {{ $activity->ip_address }}
                                    </span>

                                </td>


                                <td>

                                    <span class="badge bg-secondary">
                                        {{ strtoupper($activity->method ?? 'N/A') }}
                                    </span>

                                </td>


                                <td>

                                    <span class="route-text">
                                        {{ $activity->path ?? 'N/A' }}
                                    </span>

                                </td>


                                <td>

                                    <div class="user-agent">
                                        {{ $activity->user_agent ?: 'N/A' }}
                                    </div>

                                </td>


                                <td>

                                    @if($activity->blocked_at)

                                        {{ $activity->blocked_at->format('d M Y, h:i A') }}

                                    @else

                                        N/A

                                    @endif

                                </td>

                            </tr>

                        @endforeach

                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">

                    <h5>No Recent Activity</h5>

                    <p class="mb-0">
                        No blocked requests were found for the selected period.
                    </p>

                </div>
            @endif
        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}

    <div class="text-center text-muted py-3">

        Laravel 12 · IP Restriction Security Analytics

    </div>
</div>


{{-- ============================================================= --}}
{{-- PREPARE CHART DATA --}}
{{-- ============================================================= --}}

@php

    $chartLabels = [];

    $chartData = [];

    foreach (($sevenDayStatistics ?? []) as $item) {

        $dateValue = $item->date ?? null;

        if ($dateValue) {
            $chartLabels[] = \Carbon\Carbon::parse($dateValue)->format('d M');
        } else {
            $chartLabels[] = '';
        }

        $chartData[] = (int) ($item->total ?? 0);
    }

@endphp


{{-- ============================================================= --}}
{{-- CHART SCRIPT --}}
{{-- ============================================================= --}}

<script>

    const chartLabels = @json($chartLabels);

    const chartData = @json($chartData);

    const chartCanvas = document.getElementById('sevenDayChart');

    if (chartCanvas) {

        new Chart(chartCanvas, {

            type: 'line',
            data: {

                labels: chartLabels,

                datasets: [

                    {
                        label: 'Blocked Attempts',

                        data: chartData,

                        borderWidth: 2,

                        tension: 0.3,

                        fill: false,

                        pointRadius: 4
                    }

                ]
            },
            options: {

                responsive: true,
                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: true
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {

                            precision: 0

                        }

                    }

                }

            }

        });
    }

</script>

</body>

</html>