<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .blocked-card {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-width: 480px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card blocked-card text-center">
                    <div class="card-body p-5">
                        <div class="fs-1 mb-3">🚫</div>
                        <h2 class="fw-bold mb-3">Access Restricted</h2>
                        <p class="text-muted mb-4">
                            Your IP address (<strong>{{ $ip_address }}</strong>) has been blocked from accessing this site.
                        </p>
                        <p class="text-muted small mb-0">
                            If you believe this is an error, please contact the administrator.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
