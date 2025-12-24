<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel IP Restriction
    |--------------------------------------------------------------------------
    |
    | Enable or disable IP restriction for admin panel access.
    | When enabled, only IPs in the ADMIN_ALLOWED_IPS env variable
    | will be able to access the admin panel in production.
    |
    */
    'admin_ip_restriction_enabled' => env('ADMIN_IP_RESTRICTION', false),

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure Content Security Policy and other security headers.
    |
    */
    'csp_enabled' => env('CSP_ENABLED', true),
    'csp_report_only' => env('CSP_REPORT_ONLY', false),
    'csp_report_uri' => env('CSP_REPORT_URI', null),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for various endpoints.
    |
    */
    'rate_limits' => [
        'login' => env('RATE_LIMIT_LOGIN', 5),           // per minute
        'api' => env('RATE_LIMIT_API', 60),              // per minute
        'password_reset' => env('RATE_LIMIT_RESET', 3),  // per minute
        'file_upload' => env('RATE_LIMIT_UPLOAD', 10),   // per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    |
    | Whitelist of allowed MIME types for file uploads.
    |
    */
    'allowed_upload_mimes' => [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ],

    'max_upload_size' => env('MAX_UPLOAD_SIZE', 10240), // KB

    /*
    |--------------------------------------------------------------------------
    | Authentication Security
    |--------------------------------------------------------------------------
    */
    'password_min_length' => env('PASSWORD_MIN_LENGTH', 8),
    'password_require_special' => env('PASSWORD_REQUIRE_SPECIAL', true),
    'password_require_number' => env('PASSWORD_REQUIRE_NUMBER', true),
    'password_require_mixed_case' => env('PASSWORD_REQUIRE_MIXED_CASE', true),

    /*
    |--------------------------------------------------------------------------
    | Logging & Audit
    |--------------------------------------------------------------------------
    */
    'log_auth_events' => env('LOG_AUTH_EVENTS', true),
    'log_admin_actions' => env('LOG_ADMIN_ACTIONS', true),
    'log_file_access' => env('LOG_FILE_ACCESS', false),
];
