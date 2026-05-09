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

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'currency' => env('PAYSTACK_CURRENCY', 'GHS'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS (order notifications)
    |--------------------------------------------------------------------------
    |
    | Drivers:
    | - "sendazi" — Sendazi quick campaign API (default when SMS_API_KEY is set).
    | - "log" — writes payloads to the application log.
    | - "http" — POST JSON { to, message } to SMS_HTTP_URL.
    |
    */
    'sms' => [
        'driver' => env('SMS_DRIVER', 'sendazi'),
        'api_key' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
        'campaign_name' => env('SMS_CAMPAIGN_NAME', 'Shop order notifications'),
        'http_url' => env('SMS_HTTP_URL'),
        'http_token' => env('SMS_HTTP_TOKEN'),
    ],

    'google' => [
        'recaptcha_site_key' => env('RECAPTCHAV3_SITEKEY'),
        'recaptcha_secret_key' => env('RECAPTCHAV3_SECRET'),
    ],
];
