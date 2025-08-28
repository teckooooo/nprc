<?php

return [
    // Gateway público
    'gateway_url' => env('CORP_GATEWAY_URL', 'https://www.cablecolor.cl/API/apiNPRC/api_mia.php'),
    // Param de acción del gateway
    'gateway_action' => env('CORP_GATEWAY_ACTION', 'corpSucursal'),
    // timeout http
    'timeout' => env('CORP_API_TIMEOUT', 25),
    // Si el gateway requiere token, agréguelo; si no, déjelo vacío
    'token' => env('CORP_API_TOKEN', null),
    // Desactivar verificación SSL si fuera necesario (autofirmado)
    'verify_ssl' => env('CORP_VERIFY_SSL', true),
];
