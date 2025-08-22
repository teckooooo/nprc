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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'remote_api' => [
        'base_url' => env('REMOTE_API_BASE_URL', ''),
        'timeout'  => env('REMOTE_API_TIMEOUT', 15),
        'retries'  => env('REMOTE_API_RETRIES', 2),
        'bearer'   => env('REMOTE_API_BEARER', null),
        'index'    => env('REMOTE_API_INDEX_PATH', '/'),
        'listen'   => env('REMOTE_API_LISTEN_PATH', '/listen.php'),
        'sub'      => env('REMOTE_API_SUB_PATH', '/listen_subscription.php'),
    ],

];
