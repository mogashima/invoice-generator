<?php

return [
    'paths' => [
        'migrations' => 'db/migrations',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'invoice',
            'user' => 'root',
            'pass' => '',
            'port' => 3306,
            'charset' => 'utf8mb4',
        ],
    ],
];
