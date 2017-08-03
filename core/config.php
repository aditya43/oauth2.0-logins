<?php

return [
    'database'  => [
        'dsn'      => 'mysql:host=127.0.0.1;',
        'dbname'   => 'adi_oauth',
        'username' => 'root',
        'password' => '',
        'options'  => [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION
        ]
    ],
    'google'    => [
        'clientId'     => '',
        'clientSecret' => '',
        'callbackUrl'  => ''
    ],
    'facebook'  => [
        'clientId'     => '',
        'clientSecret' => '',
        'callbackUrl'  => ''
    ],
    'twitter'   => [
        'clientId'     => '',
        'clientSecret' => '',
        'callbackUrl'  => ''
    ],
    'linkedin'  => [
        'clientId'     => '',
        'clientSecret' => '',
        'callbackUrl'  => ''
    ],
    'microsoft' => [
        'clientId'     => '',
        'clientSecret' => '',
        'callbackUrl'  => ''
    ],
    'yahoo'     => [
        'clientId'     => '',
        'clientSecret' => '',
        'callbackUrl'  => ''
    ]
];
