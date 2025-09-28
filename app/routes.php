<?php

declare (strict_types = 1);

use App\Controllers\CustomerApiController;
use App\Controllers\CustomerController;
use App\Controllers\HelloController;
use App\Controllers\InvoiceController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {

    $app->get('/hello', [HelloController::class, 'hello']);

    $app->get('/invoices', [InvoiceController::class, 'index']);
    $app->get('/invoices/create', [InvoiceController::class, 'create']);
    $app->post('/invoices', [InvoiceController::class, 'store']);
    $app->get('/invoices/pdf', [InvoiceController::class, 'exportPdf']);

    $app->get('/customers', [CustomerController::class, 'index']);
    $app->get('/customers/create', [CustomerController::class, 'create']);
    $app->post('/customers', [CustomerController::class, 'store']);

    $app->get('/api/customers/search', [CustomerApiController::class, 'search']);

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $path = 'invoices';
        $to   = $_ENV['BASE_PATH'] ? $_ENV['BASE_PATH'] . '/' . $path : $path;
        return $response
            ->withHeader('Location', $to)
            ->withStatus(302);
    });
};
