<?php

return [

    /*
    |----------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |----------------------------------------------------------------------
    |
    | Aquí puedes configurar tus ajustes para compartir recursos entre orígenes
    | o "CORS". Esto determina qué operaciones entre orígenes pueden ejecutarse
    | en los navegadores web. Puedes ajustar estos valores según sea necesario.
    |
    | Para más información: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Rutas de la API y CSRF cookie de Sanctum

    'allowed_methods' => ['*'], // Permitir todos los métodos HTTP

    'allowed_origins' => ['*'], // Permitir todos los orígenes (desde cualquier dominio)
    // Si necesitas restringir los orígenes a algunos específicos, puedes hacerlo así:
    // 'allowed_origins' => ['https://tudominio.com'],

    'allowed_origins_patterns' => [], // Puedes usar patrones de URL si es necesario

    'allowed_headers' => ['*'], // Permitir todos los encabezados (esto incluye Authorization, Content-Type, etc.)

    'exposed_headers' => [], // Puedes exponer algunos encabezados específicos si lo necesitas

    'max_age' => 0, // Duración de la cache de CORS en segundos

    'supports_credentials' => false, // Si quieres permitir cookies, debes poner esto a true

];
