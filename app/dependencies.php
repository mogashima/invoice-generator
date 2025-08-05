<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Controllers\InvoiceController;
use App\Controllers\CustomerController;
use App\Controllers\CustomerApiController;
use App\Repositories\InvoiceRepository;
use App\Repositories\CustomerRepository;
use Slim\Views\Twig;
use Twig\Loader\FilesystemLoader;

return function (ContainerBuilder $containerBuilder) {

    // 初期のログ
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);

    // データベース
    $containerBuilder->addDefinitions([
        PDO::class => function (ContainerInterface $c) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $db = $_ENV['DB_NAME'] ?? 'invoice_db';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            return new PDO($dsn, $user, $pass, $options);
        },
    ]);

    // コントローラ
    $containerBuilder->addDefinitions([
        InvoiceController::class => function (ContainerInterface $c) {
            $twig = $c->get(Twig::class);
            $repository = $c->get(InvoiceRepository::class);
            return new InvoiceController($twig, $repository);
        },
        CustomerController::class => function (ContainerInterface $c) {
            $twig = $c->get(Twig::class);
            $repository = $c->get(CustomerRepository::class);
            return new CustomerController($twig, $repository);
        },
        CustomerApiController::class => function (ContainerInterface $c) {
            $repository = $c->get(CustomerRepository::class);
            return new CustomerApiController($repository);
        },
    ]);

    // リポジトリ
    $containerBuilder->addDefinitions([
        InvoiceRepository::class => function (ContainerInterface $c) {
            return new InvoiceRepository($c->get(PDO::class));
        },
        CustomerRepository::class => function (ContainerInterface $c) {
            return new CustomerRepository($c->get(PDO::class));
        },
    ]);
    // ビュー
    $containerBuilder->addDefinitions([
        Twig::class => function () {
            $loader = new FilesystemLoader(__DIR__ . '/../templates'); // テンプレートのディレクトリ
            return new Twig($loader);
        },
    ]);
};
