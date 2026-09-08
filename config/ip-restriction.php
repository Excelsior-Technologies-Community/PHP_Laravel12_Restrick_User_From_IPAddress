<?php

return [
    'rate_limit_max_requests' => env('IP_RATE_LIMIT_MAX_REQUESTS', 100),
    'rate_limit_window_seconds' => env('IP_RATE_LIMIT_WINDOW_SECONDS', 60),
    'auto_block_threshold' => env('IP_AUTO_BLOCK_THRESHOLD', 5),
    'send_email_alerts' => env('IP_SEND_EMAIL_ALERTS', false),
    'alert_email' => env('IP_ALERT_EMAIL', env('MAIL_FROM_ADDRESS')),
    'abuseipdb_api_key' => env('ABUSEIPDB_API_KEY', null),
    'log_retention_days' => env('IP_LOG_RETENTION_DAYS', 90),
];
