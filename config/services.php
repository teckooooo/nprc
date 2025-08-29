<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API remota (Debian) – NPRC
    |--------------------------------------------------------------------------
    | REMOTE_API_BASE_URL debe apuntar al directorio donde vive api_mia.php
    |   Ej: https://www.cablecolor.cl/webcc/public/API/apiNPRC
    |
    | Ejemplos que armará tu app:
    |   - {base_url}/api_mia.php?action=listSucursales
    |   - {base_url}/api_mia.php?action=corpSucursal&sucursal=10
    */
    'remote_api' => [
        // Base
        'base_url' => rtrim(env('REMOTE_API_BASE_URL', ''), '/'),
        'timeout'  => (int) env('REMOTE_API_TIMEOUT', 15),
        'retries'  => (int) env('REMOTE_API_RETRIES', 2),

        // SSL: true | false | ruta a cacert.pem
        // (Si en .env pones REMOTE_API_VERIFY=false, Guzzle desactiva la verificación)
        'verify'   => env('REMOTE_API_VERIFY', true),

        // Opcional: bearer/token si algún día lo necesitas
        'bearer'   => env('REMOTE_API_BEARER'),

        // Subrutas utilitarias (por si en algún momento las ocupas)
        'index_path'        => env('REMOTE_API_INDEX_PATH', '/'),
        'listen_path'       => env('REMOTE_API_LISTEN_PATH', '/listen.php'),
        'subscription_path' => env('REMOTE_API_SUB_PATH', '/listen_subscription.php'),

        // Nombre de acciones en api_mia.php (tu app las concatena como query ?action=…)
        'actions' => [
            'list_sucursales' => 'listSucursales',
            'corp_sucursal'   => 'corpSucursal',
            'ping_sucursal'   => 'pingSucursal',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pasarela corporativos (opcional)
    |--------------------------------------------------------------------------
    | Si prefieres ir por el "gateway" público en vez de golpear directo
    | a cada sucursal, configura estos valores en tu .env.
    */
    'corp_gateway' => [
        'url'       => env('CORP_GATEWAY_URL'),             // p.ej. https://www.cablecolor.cl/API/apiNPRC/api_mia.php
        'action'    => env('CORP_GATEWAY_ACTION', 'corpSucursal'),
        'timeout'   => (int) env('CORP_API_TIMEOUT', 25),
        'verify'    => env('CORP_VERIFY_SSL', true),        // true/false/ruta a cacert.pem
        'bearer'    => env('CORP_API_TOKEN'),               // si aplica
    ],

    /*
    |--------------------------------------------------------------------------
    | NPRC API base para el contrato (usada por ContratoController)
    |--------------------------------------------------------------------------
    | Opción B: si no hay NPRC_API_URL en .env, usa CORP_GATEWAY_URL.
    */
    'nprc_api' => [
        'base' => env('NPRC_API_URL', env('CORP_GATEWAY_URL', '')),
    ],

];
