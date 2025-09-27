<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$driver = $_ENV['DB_DRIVER'] ?? 'mysql';

if ($driver === 'sqlite') {
    // SQLite の場合
    $dbFile    = __DIR__ . '/db/sqlite/' . ($_ENV['DB_NAME'] ?? '.sqlite');
    $envConfig = [
        'adapter' => 'sqlite',
        'name'    => $dbFile,
        'charset' => 'utf8mb4',
    ];
} else {
    // MySQL の場合
    $envConfig = [
        'adapter' => 'mysql',
        'host'    => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'name'    => $_ENV['DB_DATABASE'] ?? 'invoice',
        'user'    => $_ENV['DB_USERNAME'] ?? 'root',
        'pass'    => $_ENV['DB_PASSWORD'] ?? '',
        'port'    => $_ENV['DB_PORT'] ?? 3306,
        'charset' => 'utf8mb4',
    ];
}

return [
    'paths'        => [
        'migrations' => 'db/migrations',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'development',
        'development'             => $envConfig,
    ],
];
