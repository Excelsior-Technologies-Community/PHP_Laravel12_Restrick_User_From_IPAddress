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
            background: var(--bg-card);
            transition: background 0.3s;
            border: 1px solid var(--border-color);
        }
        .stat-card h6 {
            color: var(--text-muted);
        }
        .stat-card h2 {
            color: var(--text-color);
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
        .text-muted {
            color: var(--text-muted) !important;
        }
        .bulk-actions-bar {
            display: none;
            position: sticky;
            top: 10px;
            z-index: 100;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-color);
        }
        .bulk-actions-bar.visible {
            display: flex;
        }
        .type-badge {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .alert {
            background: var(--alert-bg);
            color: var(--text-color);
            border-color: var(--border-color);
        }
        .alert-success {
            --bs-alert-color: #0f5132;
            --bs-alert-bg: #d1e7dd;
            --bs-alert-border-color: #badbcc;
        }
        [data-theme="dark"] .alert-success {
            --bs-alert-color: #c3e6cb;
            --bs-alert-bg: #143b26;
            --bs-alert-border-color: #1b5e34;
        }
        .alert-danger {
            --bs-alert-color: #842029;
            --bs-alert-bg: #f8d7da;
            --bs-alert-border-color: #f5c2c7;
        }
        [data-theme="dark"] .alert-danger {
            --bs-alert-color: #f5c2c7;
            --bs-alert-bg: #3a1620;
            --bs-alert-border-color: #6a2c36;
        }
        .alert-info {
            --bs-alert-color: #055160;
            --bs-alert-bg: #cff4fc;
            --bs-alert-border-color: #b6effb;
        }
        [data-theme="dark"] .alert-info {
            --bs-alert-color: #cff4fc;
            --bs-alert-bg: #102b32;
            --bs-alert-border-color: #164b54;
        }
        code {
            background: var(--bg-input);
            color: #ff7b72;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
        }
        .dropdown-menu {
            background: var(--bg-card);
            border-color: var(--border-color);
        }
        .dropdown-item {
            color: var(--text-color);
        }
        .dropdown-item:hover {
            background: var(--table-hover);
        }
        .btn-close {
            filter: invert(1);
        }
        [data-theme="light"] .btn-close {
            filter: invert(0);
        }
        .card-header {
            background: var(--bg-card);
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
        }
        .small, small {
            color: var(--text-muted);
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
        .btn {
            font-weight: 500;
        }
        .btn-outline-secondary {
            color: var(--text-color);
            border-color: var(--border-color);
        }
        .btn-outline-secondary:hover {
            background: var(--bg-input);
            color: var(--text-color);
        }
        .btn-sm {
            font-size: 0.875rem;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
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
        .small, small {
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="container py-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="page-title mb-1">IP Restriction Dashboard</h1>
                <p class="text-muted mb-0">Manage blocked IP addresses and monitor access attempts.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('security-analytics.index') }}" class="btn btn-dark">
                    📊 Security Analytics
                </a>
                <form action="{{ route('ip-restrictions.dark-mode.toggle') }}" method="GET" class="d-inline">
                    <button type="submit" class="btn btn-outline-secondary">
                        {{ $darkMode == '1' ? '☀️ Light' : '🌙 Dark' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Validation Errors --}}
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
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Total Restrictions</h6>
                        <h2 class="mb-0">{{ $restrictions->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Active Restrictions</h6>
                        <h2 class="mb-0">
                            {{ $restrictions->filter(fn ($r) => $r->isCurrentlyBlocked())->count() }}
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Blocked Attempts</h6>
                        <h2 class="mb-0">{{ $logs->total() }}</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add IP Restriction --}}
        <div class="card dashboard-card mb-4">
            <div class="card-body p-4">
                <h4 class="mb-4">Add IP Restriction</h4>
                <form action="{{ route('ip-restrictions.store') }}" method="POST" id="addRestrictionForm">
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
                        <div class="col-md-2">
                            <label class="form-label">Block Until</label>
                            <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                            <small class="text-muted">Leave empty for permanent</small>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-danger" id="submitBtn">Block IP Address</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="card dashboard-card mb-4">
            <div class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Search Restrictions</label>
                        <form action="{{ route('ip-restrictions.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control" placeholder="Search IP, reason..." value="{{ $search ?? '' }}">
                            <button type="submit" class="btn btn-outline-secondary">🔍</button>
                        </form>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <form action="{{ route('ip-restrictions.index') }}" method="GET">
                            <select name="type" class="form-select" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="single" {{ ($filterType ?? '') == 'single' ? 'selected' : '' }}>Single IP</option>
                                <option value="cidr" {{ ($filterType ?? '') == 'cidr' ? 'selected' : '' }}>CIDR</option>
                                <option value="country" {{ ($filterType ?? '') == 'country' ? 'selected' : '' }}>Country</option>
                                <option value="whitelist" {{ ($filterType ?? '') == 'whitelist' ? 'selected' : '' }}>Whitelist</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <form action="{{ route('ip-restrictions.index') }}" method="GET">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="active" {{ ($filterStatus ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="allowed" {{ ($filterStatus ?? '') == 'allowed' ? 'selected' : '' }}>Allowed</option>
                                <option value="expired" {{ ($filterStatus ?? '') == 'expired' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </form>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bulk Import</label>
                        <form action="{{ route('ip-restrictions.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                            @csrf
                            <input type="file" name="import_file" class="form-control form-control-sm" accept=".csv" required>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Import</button>
                        </form>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Export</label>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('ip-restrictions.export', 'csv') }}">CSV</a></li>
                                <li><a class="dropdown-item" href="{{ route('ip-restrictions.export', 'json') }}">JSON</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll(this)">
                    </div>
                </div>
            </div>
        </div>
        {{-- IP Restrictions --}}
        <div class="card dashboard-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Blocked IP Addresses</h4>
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
                                    <th><input type="checkbox" onchange="toggleSelectAll(this)"></th>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>IP Address</th>
                                    <th>CIDR / Country</th>
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
                                        <input type="checkbox" name="ids[]" value="{{ $restriction->id }}" class="bulk-check" onchange="updateBulkBar()">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="badge bg-secondary type-badge">{{ $restriction->restriction_type }}</span>
                                    </td>
                                    <td>
                                        <span class="ip-address">{{ $restriction->ip_address }}</span>
                                        @if($restriction->restriction_type === 'whitelist')
                                        <i class="bi bi-shield-check text-success ms-1" title="Whitelisted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        @if($restriction->restriction_type === 'cidr' && $restriction->cidr)
                                        <code>{{ $restriction->cidr }}</code>
                                        @elseif($restriction->restriction_type === 'country' && $restriction->country_code)
                                        <span class="badge bg-info">{{ $restriction->country_code }}</span>
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $restriction->reason ?? 'No reason provided' }}</td>
                                    <td>
                                        @if($restriction->expires_at)
                                        {{ $restriction->expires_at->format('d M Y, h:i A') }}
                                        @if($restriction->expires_at->isPast())
                                        <br><span class="text-muted">Expired</span>
                                        @endif
                                        @else
                                        <span class="text-danger">Permanent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($restriction->isCurrentlyBlocked())
                                        <span class="badge bg-danger status-badge">Blocked</span>
                                        @else
                                        <span class="badge bg-success status-badge">Allowed</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($restriction->is_active)
                                            <form action="{{ route('ip-restrictions.deactivate', $restriction) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-warning">Disable</button>
                                            </form>
                                            @else
                                            <form action="{{ route('ip-restrictions.activate', $restriction) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">Enable</button>
                                            </form>
                                            @endif
                                            <form action="{{ route('ip-restrictions.destroy', $restriction) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this IP restriction?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
                @else
                <div class="alert alert-info mb-0">No IP restrictions have been configured yet.</div>
                @endif
            </div>
        </div>

        {{-- Blocked Access Logs --}}
        <div class="card dashboard-card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h4 class="mb-0">Blocked Access Logs</h4>
                    <div class="d-flex gap-2 align-items-center">
                        <form action="{{ route('ip-restrictions.index') }}" method="GET" class="d-flex gap-2">
                            <input type="text" name="log_search" class="form-control form-control-sm" placeholder="Search logs..." value="{{ $logSearch ?? '' }}">
                            <select name="log_method" class="form-select form-select-sm">
                                <option value="">All Methods</option>
                                <option value="GET" {{ ($logMethod ?? '') == 'GET' ? 'selected' : '' }}>GET</option>
                                <option value="POST" {{ ($logMethod ?? '') == 'POST' ? 'selected' : '' }}>POST</option>
                                <option value="PUT" {{ ($logMethod ?? '') == 'PUT' ? 'selected' : '' }}>PUT</option>
                                <option value="DELETE" {{ ($logMethod ?? '') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
                            </select>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $logDateFrom ?? '' }}">
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $logDateTo ?? '' }}">
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                        </form>
                        @if($logs->count())
                        <div class="d-flex gap-2">
                            <a href="{{ route('ip-restrictions.logs.export') }}" class="btn btn-sm btn-outline-success">📥 Export CSV</a>
                            <form action="{{ route('ip-restrictions.logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all logs?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Clear Logs</button>
                            </form>
                        </div>
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
                                <th>Country/City</th>
                                <th>Method</th>
                                <th>Requested URL</th>
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
                                <td><span class="badge bg-secondary">{{ $log->method }}</span></td>
                                <td>/{{ $log->path }}</td>
                                <td style="max-width: 300px;"><small>{{ $log->user_agent ?? 'Unknown' }}</small></td>
                                <td>{{ $log->blocked_at->format('d M Y, h:i:s A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $logs->links() }}</div>
                @else
                <div class="alert alert-success mb-0">No blocked access attempts have been recorded.</div>
                @endif
            </div>
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
