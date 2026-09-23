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
    'ajira' => [
        'url' => env('AJIRA_API_URL'),
        'api_url' => env('AJIRA_API_URL'),
        'zanid_endpoint' => env('AJIRA_ZANID_ENDPOINT'),
        'token' => env('AJIRA_API_TOKEN'),
        'timeout' => (int) env('AJIRA_API_TIMEOUT', 30),
        'connect_timeout' => (int) env('AJIRA_API_CONNECT_TIMEOUT', 10),
        'signing_certificate_path' => env('SIGNING_CERTIFICATE_PATH'),
        'signing_certificate_file' => env('SIGNING_CERTIFICATE_FILE'),
        'signing_certificate_password' => env('SIGNING_CERTIFICATE_PASSWORD'),
    ],

    'certificate' => [
        'path' => env('SIGNING_CERTIFICATE_PATH', 'certificate'),
        'file' => env('SIGNING_CERTIFICATE_FILE'),
        'password' => env('SIGNING_CERTIFICATE_PASSWORD'),
        'alias' => env('SIGNING_CERTIFICATE_ALIAS', ''),
    ],
];
