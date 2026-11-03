<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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
    'kepegawaian' => [
        'url'     => env('PEGAWAI_API_URL'),
        'api_key' => env('PEGAWAI_API_KEY'),
    ],

    'fingerspot' => [
        'api_token'     => env('FINGERSPOT_API_TOKEN'),
        'cloud_ids'     => array_values(array_filter(array_map('trim', explode(',', (string) env('FINGERSPOT_CLOUD_IDS', ''))))),
        'base_url'      => env('FINGERSPOT_BASE_URL', 'https://developer.fingerspot.io/api'),
        'max_jam_masuk' => env('FINGERSPOT_MAX_MASUK', '10:00:00'),
    ],

];
