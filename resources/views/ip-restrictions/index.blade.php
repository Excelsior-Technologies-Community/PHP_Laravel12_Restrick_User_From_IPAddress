<!DOCTYPE html>
<html lang="en" data-theme="{{ $darkMode == '1' ? 'dark' : 'light' }}">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Restriction Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --alert-bg: #ffffff;
            --badge-bg: #6c757d;
            --badge-color: #ffffff;
            --thead-bg: #f8f9fa;
            --thead-color: #212529;
            --link-color: #0d6efd;
            --overlay-bg: rgba(255,255,255,0.85);
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
            --alert-bg: #15181e;
            --badge-bg: #2a2f3a;
            --badge-color: #e4e6eb;
            --thead-bg: #1c1f27;
            --thead-color: #e4e6eb;
            --link-color: #7aa6ff;
            --overlay-bg: rgba(11,13,16,0.85);
        }
        body {
            background: var(--bg-body);
            color: var(--text-color);
            transition: background 0.3s, color 0.3s;
        }
        .dashboard-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 20px var(--shadow-color);
            background: var(--bg-card);
            transition: background 0.3s;
            border: 1px solid var(--border-color);
        }
        .page-title {
            font-weight: 700;
        }
        .status-badge {
            min-width: 80px;
        }
        .table td, .table th {
            vertical-align: middle;
            border-color: var(--border-color);
            color: var(--text-color);
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
                        <div class="col-md-3">
                            <label class="form-label">Restriction Type</label>
                            <select name="restriction_type" class="form-select" id="restrictionType" onchange="toggleTypeFields()">
                                <option value="single">Single IP</option>
                                <option value="cidr">CIDR Range</option>
                                <option value="country">Country</option>
                                <option value="whitelist">Whitelist</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IP Address / CIDR</label>
                            <input type="text" name="ip_address" class="form-control" placeholder="192.168.1.100" value="{{ old('ip_address') }}" id="ipAddressField" required>
                            <small class="text-muted" id="ipHint">Enter a valid IP address</small>
                        </div>
                        <div class="col-md-2" id="cidrField" style="display:none;">
                            <label class="form-label">CIDR Notation</label>
                            <input type="text" name="cidr" class="form-control" placeholder="192.168.1.0/24" value="{{ old('cidr') }}">
                        </div>
                        <div class="col-md-2" id="countryField" style="display:none;">
                            <label class="form-label">Country Code</label>
                            <input type="text" name="country_code" class="form-control" placeholder="US" value="{{ old('country_code') }}" maxlength="2" style="text-transform: uppercase;">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Reason</label>
                            <input type="text" name="reason" class="form-control" placeholder="Suspicious activity" value="{{ old('reason') }}">
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
                    <form action="{{ route('ip-restrictions.bulk-action') }}" method="POST" id="restrictionsForm">
                        @csrf
                        {{-- Bulk Actions Bar --}}
                        <div id="bulkActionsBar" class="bulk-actions-bar justify-content-between align-items-center mb-3 p-3 rounded dashboard-card">
                            <div>
                                <span id="selectedCount" class="fw-bold">0 selected</span>
                            </div>
                            <div class="d-flex gap-2">
                                <input type="hidden" name="action" id="bulkActionType" value="activate">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-success" onclick="setBulkAction('activate')">Activate</button>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="setBulkAction('deactivate')">Deactivate</button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="setBulkAction('delete')">Delete</button>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary ms-2" onclick="return confirmBulkAction()">Apply</button>
                            </div>
                        </div>

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
                                <td>{{ $logs->firstItem() + $loop->index }}</td>
                                <td><span class="ip-address">{{ $log->ip_address }}</span></td>
                                <td>
                                    @if($log->country_code)
                                    <span class="badge bg-info">{{ $log->country_code }}</span>
                                    @endif
                                    @if($log->city)
                                    <small class="text-muted d-block">{{ $log->city }}</small>
                                    @endif
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleTypeFields() {
            const type = document.getElementById('restrictionType').value;
            const cidrField = document.getElementById('cidrField');
            const countryField = document.getElementById('countryField');
            const ipAddressField = document.getElementById('ipAddressField');
            const ipHint = document.getElementById('ipHint');
            const submitBtn = document.getElementById('submitBtn');

            cidrField.style.display = 'none';
            countryField.style.display = 'none';
            ipAddressField.required = true;

            if (type === 'cidr') {
                cidrField.style.display = 'block';
                ipHint.textContent = 'Enter a valid IP address (e.g., 192.168.1.100)';
                submitBtn.textContent = 'Block CIDR Range';
            } else if (type === 'country') {
                countryField.style.display = 'block';
                ipHint.textContent = 'Enter any IP address for country lookup';
                submitBtn.textContent = 'Block Country';
            } else if (type === 'whitelist') {
                ipHint.textContent = 'Enter IP to whitelist';
                submitBtn.textContent = 'Whitelist IP';
            } else {
                ipHint.textContent = 'Enter a valid IP address';
                submitBtn.textContent = 'Block IP Address';
            }
        }

        function toggleSelectAll(checkbox) {
            document.querySelectorAll('.bulk-check').forEach(cb => cb.checked = checkbox.checked);
            updateBulkBar();
        }

        function updateBulkBar() {
            const selected = document.querySelectorAll('.bulk-check:checked').length;
            const bar = document.getElementById('bulkActionsBar');
            const count = document.getElementById('selectedCount');
            count.textContent = selected + ' selected';
            if (selected > 0) {
                bar.classList.add('visible');
            } else {
                bar.classList.remove('visible');
            }
        }

        function setBulkAction(action) {
            document.getElementById('bulkActionType').value = action;
        }

        function confirmBulkAction() {
            const action = document.getElementById('bulkActionType').value;
            const count = document.querySelectorAll('.bulk-check:checked').length;
            if (count === 0) {
                alert('Please select at least one item.');
                return false;
            }
            const confirmMessage = action === 'delete'
                ? `Are you sure you want to delete ${count} restriction(s)?`
                : `Are you sure you want to ${action} ${count} restriction(s)?`;
            return confirm(confirmMessage);
        }
    </script>
</body>
</html>
