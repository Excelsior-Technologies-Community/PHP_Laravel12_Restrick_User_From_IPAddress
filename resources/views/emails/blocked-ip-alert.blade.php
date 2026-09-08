<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blocked IP Alert</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f7fb; padding: 40px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <h2 style="color: #dc3545;">🚨 Blocked IP Access Alert</h2>
        <p>A blocked IP address attempted to access your application:</p>
        <ul>
            <li><strong>IP Address:</strong> {{ $ipAddress }}</li>
            <li><strong>Path:</strong> {{ $path }}</li>
            <li><strong>Method:</strong> {{ $method }}</li>
            <li><strong>Time:</strong> {{ now()->format('d M Y, h:i A') }}</li>
        </ul>
        <p style="color: #6c757d; font-size: 14px;">
            This is an automated alert from your IP Restriction Management system.
        </p>
    </div>
</body>
</html>
