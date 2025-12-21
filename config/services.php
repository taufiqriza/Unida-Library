<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | KUBUKU E-Library API
    |--------------------------------------------------------------------------
    |
    | Configuration for KUBUKU digital library integration.
    | API provides access to subscribed e-book collection.
    |
    */

    'kubuku' => [
        'enabled' => env('KUBUKU_ENABLED', true),
        'api_key' => env('KUBUKU_API_KEY'),
        'base_url' => env('KUBUKU_BASE_URL', 'https://kubuku.id/api/wl'),
        'cache_ttl' => env('KUBUKU_CACHE_TTL', 3600), // 1 hour
        'search_limit' => env('KUBUKU_SEARCH_LIMIT', 20),
    ],

];
