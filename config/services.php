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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'jitsi' => [
        'domain' => env('JITSI_DOMAIN', 'meet.jit.si'),
    ],

    'paddle' => [
        'api_key' => env('PADDLE_API_KEY'),
        'vendor_id' => env('PADDLE_VENDOR_ID'),
        'client_token' => env('PADDLE_CLIENT_SIDE_TOKEN'),
        'product_id' => env('PADDLE_PRODUCT_ID'), // Add a default product ID for therapy sessions
        'environment' => env('PADDLE_ENVIRONMENT', 'sandbox'), // sandbox or production

        // Pre-configured price IDs for each session duration
        'prices' => [
            '30min' => env('PADDLE_PRICE_30MIN'),
            '60min' => env('PADDLE_PRICE_60MIN'),
            '90min' => env('PADDLE_PRICE_90MIN'),
            '120min' => env('PADDLE_PRICE_120MIN'),
            // Optional additional durations
            // '45min' => env('PADDLE_PRICE_45MIN'),
            // '150min' => env('PADDLE_PRICE_150MIN'),
            // '180min' => env('PADDLE_PRICE_180MIN'),
        ],
    ]

];
