<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>IP Restriction Management</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .dashboard-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .page-title {
            font-weight: 700;
        }

        .status-badge {
            min-width: 80px;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .ip-address {
            font-family: monospace;
            font-weight: 600;
        }

        .stat-card {
            border-radius: 14px;
            border: 0;
        }

        .filter-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
    </style>

</head>


<body>

    <div class="container-fluid px-3 px-md-5 py-5">

        {{-- Header --}}

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

            <div>

                <h1 class="page-title mb-1">
                    🛡️ IP Restriction Dashboard
                </h1>

                <p class="text-muted mb-0">
                    Manage blocked IP addresses and monitor access attempts.
                </p>

            </div>


            <div class="d-flex gap-2">

                <a
                    href="{{ route('security-analytics.index') }}"
                    class="btn btn-dark">
                    📊 Analytics
                </a>

                <a
                    href="{{ route('ip-restrictions.export') }}"
                    class="btn btn-success">
                    📥 Export IPs
                </a>

            </div>

        </div>


        {{-- Success --}}

        @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>

        </div>

        @endif


        {{-- Errors --}}

        @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please fix the following errors:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

        @endif


        {{-- Statistics --}}

        <div class="row g-4 mb-4">

            <div class="col-12 col-md-3">

                <div class="card stat-card shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Restrictions
                        </small>

                        <h2 class="fw-bold mb-0">
                            {{ number_format($totalRestrictions) }}
                        </h2>

                    </div>

                </div>

            </div>


            <div class="col-12 col-md-3">

                <div class="card stat-card shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Active Restrictions
                        </small>

                        <h2 class="fw-bold text-danger mb-0">
                            {{ number_format($activeRestrictions) }}
                        </h2>

                    </div>

                </div>

            </div>


            <div class="col-12 col-md-3">

                <div class="card stat-card shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Expired
                        </small>

                        <h2 class="fw-bold text-warning mb-0">
                            {{ number_format($expiredRestrictions) }}
                        </h2>

                    </div>

                </div>

            </div>


            <div class="col-12 col-md-3">

                <div class="card stat-card shadow-sm h-100">

                    <div class="card-body">

                        <small class="text-muted">
                            Blocked Attempts
                        </small>

                        <h2 class="fw-bold text-primary mb-0">
                            {{ number_format($blockedAttempts) }}
                        </h2>

                    </div>

                </div>

            </div>

        </div>


        {{-- Add IP --}}

        <div class="card dashboard-card mb-4">

            <div class="card-body p-4">

                <h4 class="mb-4">
                    ➕ Add IP Restriction
                </h4>


                <form
                    action="{{ route('ip-restrictions.store') }}"
                    method="POST">

                    @csrf

                    <div class="row g-3">

                        <div class="col-md-4">

                            <label class="form-label">
                                IP Address
                            </label>

                            <input
                                type="text"
                                name="ip_address"
                                class="form-control"
                                placeholder="192.168.1.100"
                                value="{{ old('ip_address') }}"
                                required>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Reason
                            </label>

                            <input
                                type="text"
                                name="reason"
                                class="form-control"
                                placeholder="Suspicious activity"
                                value="{{ old('reason') }}">

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Block Until
                            </label>

                            <input
                                type="datetime-local"
                                name="expires_at"
                                class="form-control"
                                value="{{ old('expires_at') }}">

                            <small class="text-muted">
                                Empty = permanent block
                            </small>

                        </div>


                        <div class="col-12">

                            <button
                                type="submit"
                                class="btn btn-danger">
                                🚫 Block IP Address
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        {{-- Search / Filter --}}

        <div class="filter-card p-4 mb-4">

            <form
                method="GET"
                action="{{ route('ip-restrictions.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- Search --}}

                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            🔎 Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search IP or reason..."
                            value="{{ $search }}">

                    </div>


                    {{-- Status --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            🎯 Status
                        </label>

                        <select
                            name="status"
                            class="form-select">

                            <option
                                value="all"
                                {{ $status === 'all' ? 'selected' : '' }}>
                                All
                            </option>

                            <option
                                value="active"
                                {{ $status === 'active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option
                                value="disabled"
                                {{ $status === 'disabled' ? 'selected' : '' }}>
                                Disabled
                            </option>

                            <option
                                value="expired"
                                {{ $status === 'expired' ? 'selected' : '' }}>
                                Expired
                            </option>

                        </select>

                    </div>


                    {{-- Sort --}}

                    <div class="col-md-3">

                        <label class="form-label fw-semibold">
                            ↕️ Sort
                        </label>

                        <select
                            name="sort"
                            class="form-select">

                            <option
                                value="latest"
                                {{ $sort === 'latest' ? 'selected' : '' }}>
                                Newest First
                            </option>

                            <option
                                value="oldest"
                                {{ $sort === 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>

                            <option
                                value="ip_asc"
                                {{ $sort === 'ip_asc' ? 'selected' : '' }}>
                                IP A-Z
                            </option>

                            <option
                                value="ip_desc"
                                {{ $sort === 'ip_desc' ? 'selected' : '' }}>
                                IP Z-A
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}

                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-primary w-100">
                            Apply
                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- Restrictions --}}

        <div class="card dashboard-card mb-4">

            <div class="card-body p-4">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">

                    <div>

                        <h4 class="mb-1">
                            🚫 Blocked IP Addresses
                        </h4>

                        <small class="text-muted">
                            Showing {{ $restrictions->count() }}
                            of {{ $restrictions->total() }}
                            restrictions
                        </small>

                    </div>

                </div>


                @if($restrictions->count())

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>IP Address</th>

                                <th>Reason</th>

                                <th>Expires</th>

                                <th>Status</th>

                                <th>Actions</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($restrictions as $restriction)

                            <tr>

                                <td>
                                    {{ $restrictions->firstItem() + $loop->index }}
                                </td>


                                <td>

                                    <span class="ip-address">
                                        {{ $restriction->ip_address }}
                                    </span>

                                </td>


                                <td>
                                    {{ $restriction->reason ?? 'No reason provided' }}
                                </td>


                                <td>

                                    @if($restriction->expires_at)

                                    {{ $restriction->expires_at->format(
                                                'd M Y, h:i A'
                                            ) }}

                                    @if($restriction->expires_at->isPast())

                                    <br>

                                    <span class="text-danger">
                                        Expired
                                    </span>

                                    @endif

                                    @else

                                    <span class="text-danger">
                                        Permanent
                                    </span>

                                    @endif

                                </td>


                                <td>

                                    @if($restriction->isCurrentlyBlocked())

                                    <span class="badge bg-danger status-badge">
                                        Blocked
                                    </span>

                                    @elseif(
                                    $restriction->is_active &&
                                    $restriction->expires_at &&
                                    $restriction->expires_at->isPast()
                                    )

                                    <span class="badge bg-warning text-dark status-badge">
                                        Expired
                                    </span>

                                    @else

                                    <span class="badge bg-success status-badge">
                                        Allowed
                                    </span>

                                    @endif

                                </td>


                                <td>

                                    <div class="d-flex gap-2 flex-wrap">

                                        @if($restriction->is_active)

                                        <form
                                            action="{{ route(
                                                        'ip-restrictions.deactivate',
                                                        $restriction
                                                    ) }}"
                                            method="POST">

                                            @csrf

                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-warning">
                                                Disable
                                            </button>

                                        </form>

                                        @else

                                        <form
                                            action="{{ route(
                                                        'ip-restrictions.activate',
                                                        $restriction
                                                    ) }}"
                                            method="POST">

                                            @csrf

                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-success">
                                                Enable
                                            </button>

                                        </form>

                                        @endif


                                        <form
                                            action="{{ route(
                                                    'ip-restrictions.destroy',
                                                    $restriction
                                                ) }}"
                                            method="POST"
                                            onsubmit="return confirm(
                                                    'Are you sure you want to delete this IP restriction?'
                                                );">

                                            @csrf

                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger">
                                                Delete
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}

                <div class="mt-3">

                    {{ $restrictions->links() }}

                </div>

                @else

                <div class="alert alert-info mb-0">

                    No IP restrictions found.

                </div>

                @endif

            </div>

        </div>


        {{-- Logs --}}

        <div class="card dashboard-card">

            <div class="card-body p-4">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">

                    <div>

                        <h4 class="mb-1">
                            🚨 Blocked Access Logs
                        </h4>

                        <small class="text-muted">
                            {{ number_format($logs->total()) }}
                            total blocked attempts
                        </small>

                    </div>


                    <div class="d-flex gap-2">

                        <a
                            href="{{ route('ip-restrictions.logs.export') }}"
                            class="btn btn-sm btn-success">
                            📥 Export Logs
                        </a>


                        @if($logs->total())

                        <form
                            action="{{ route(
                                'ip-restrictions.logs.clear'
                            ) }}"
                            method="POST"
                            onsubmit="return confirm(
                                'Are you sure you want to clear all logs?'
                            );">

                            @csrf

                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-danger">
                                🗑 Clear Logs
                            </button>

                        </form>

                        @endif

                    </div>

                </div>


                @if($logs->count())

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>IP Address</th>

                                <th>Method</th>

                                <th>URL</th>

                                <th>User Agent</th>

                                <th>Blocked At</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($logs as $log)

                            <tr>

                                <td>
                                    {{ $logs->firstItem() + $loop->index }}
                                </td>


                                <td>

                                    <span class="ip-address">
                                        {{ $log->ip_address }}
                                    </span>

                                </td>


                                <td>

                                    <span class="badge bg-secondary">
                                        {{ $log->method ?? 'N/A' }}
                                    </span>

                                </td>


                                <td>
                                    /{{ $log->path }}
                                </td>


                                <td style="max-width: 300px;">

                                    <small>
                                        {{ $log->user_agent ?? 'Unknown' }}
                                    </small>

                                </td>


                                <td>

                                    {{ $log->blocked_at
                                            ? $log->blocked_at->format(
                                                'd M Y, h:i:s A'
                                            )
                                            : 'N/A'
                                        }}

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Log Pagination --}}

                <div class="mt-3">

                    {{ $logs->links() }}

                </div>

                @else

                <div class="alert alert-success mb-0">

                    No blocked access attempts have been recorded.

                </div>

                @endif

            </div>

        </div>


        {{-- Footer --}}

        <div class="text-center text-muted py-4">

            <small>
                IP Restriction Security System
                &copy; {{ date('Y') }}
            </small>

        </div>

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>