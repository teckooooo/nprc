<?php
// config/auth.php
return [

    'defaults' => [
        // si tu app solo usará corporativos, puedes dejarlo como default
        'guard' => 'corporativos',
        'passwords' => 'corporativos',
    ],

'guards' => [
  'corporativos' => ['driver' => 'session', 'provider' => 'corporativos'],
],
'providers' => [
  'corporativos' => ['driver' => 'eloquent', 'model' => App\Models\Corporativo::class],
],


    'passwords' => [
        'corporativos' => [
            'provider' => 'corporativos',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],
];
