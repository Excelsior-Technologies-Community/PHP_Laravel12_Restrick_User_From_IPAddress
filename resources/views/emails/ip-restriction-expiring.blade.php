<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Restriction Expiring</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f5f7fb; padding: 40px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
        <h2 style="color: #ffc107;">⏰ IP Restriction Expiring Soon</h2>
        <p>The following IP restriction will expire within 7 days:</p>
        <ul>
            <li><strong>IP Address:</strong> {{ $restriction->ip_address }}</li>
            <li><strong>Type:</strong> {{ ucfirst($restriction->restriction_type) }}</li>
            <li><strong>Reason:</strong> {{ $restriction->reason ?? 'N/A' }}</li>
            <li><strong>Expires At:</strong> {{ $restriction->expires_at->format('d M Y, h:i A') }}</li>
        </ul>
        <p style="color: #6c757d; font-size: 14px;">
            Please review and renew the restriction if necessary.
        </p>
    </div>
</body>
</html>
