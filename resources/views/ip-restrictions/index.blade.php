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
    </style>
</head>

<body>

    <div class="container py-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="page-title mb-1">
                    IP Restriction Dashboard
                </h1>

                <p class="text-muted mb-0">
                    Manage blocked IP addresses and monitor access attempts.
                </p>
            </div>

            <div>
                <a
                    href="{{ route('security-analytics.index') }}"
                    class="btn btn-dark">
                    📊 Security Analytics
                </a>
            </div>

        </div>


        {{-- Success Message --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"></button>
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

                        <h6 class="text-muted">
                            Total Restrictions
                        </h6>

                        <h2 class="mb-0">
                            {{ $restrictions->count() }}
                        </h2>

                    </div>
                </div>

            </div>


            <div class="col-md-4">

                <div class="card stat-card shadow-sm">
                    <div class="card-body">

                        <h6 class="text-muted">
                            Active Restrictions
                        </h6>

                        <h2 class="mb-0">

                            {{
                            $restrictions
                                ->filter(fn ($restriction) =>
                                    $restriction->isCurrentlyBlocked()
                                )
                                ->count()
                        }}

                        </h2>

                    </div>
                </div>

            </div>


            <div class="col-md-4">

                <div class="card stat-card shadow-sm">
                    <div class="card-body">

                        <h6 class="text-muted">
                            Blocked Attempts
                        </h6>

                        <h2 class="mb-0">
                            {{ $logs->total() }}
                        </h2>

                    </div>
                </div>

            </div>

        </div>


        {{-- Add IP --}}
        <div class="card dashboard-card mb-4">

            <div class="card-body p-4">

                <h4 class="mb-4">
                    Add IP Restriction
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
                                Leave empty for permanent blocking.
                            </small>

                        </div>


                        <div class="col-12">

                            <button
                                type="submit"
                                class="btn btn-danger">
                                Block IP Address
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        {{-- IP Restrictions --}}
        <div class="card dashboard-card mb-4">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h4 class="mb-0">
                        Blocked IP Addresses
                    </h4>

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
                                    {{ $loop->iteration }}
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

                                    {{ $restriction->expires_at->format('d M Y, h:i A') }}

                                    @if($restriction->expires_at->isPast())
                                    <br>
                                    <span class="text-muted">
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

                                    @else

                                    <span class="badge bg-success status-badge">
                                        Allowed
                                    </span>

                                    @endif

                                </td>

                                <td>

                                    <div class="d-flex gap-2">

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
                                                'Are you sure you want to remove this IP restriction?'
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

                @else

                <div class="alert alert-info mb-0">
                    No IP restrictions have been configured yet.
                </div>

                @endif

            </div>

        </div>


        {{-- Blocked Access Logs --}}
        <div class="card dashboard-card">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h4 class="mb-0">
                        Blocked Access Logs
                    </h4>


                    @if($logs->count())

                    <form
                        action="{{ route('ip-restrictions.logs.clear') }}"
                        method="POST"
                        onsubmit="return confirm(
                            'Are you sure you want to clear all logs?'
                        );">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger">
                            Clear Logs
                        </button>

                    </form>

                    @endif

                </div>


                @if($logs->count())

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>IP Address</th>
                                <th>Method</th>
                                <th>Requested URL</th>
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
                                        {{ $log->method }}
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
                                    {{ $log->blocked_at->format(
                                        'd M Y, h:i:s A'
                                    ) }}
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


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

    </div>


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>