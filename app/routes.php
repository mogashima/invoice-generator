<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use App\Controllers\InvoiceController;
use App\Controllers\CustomerController;
use App\Controllers\CustomerApiController;
use App\Controllers\HelloController;

return function (App $app) {

    $app->get('/hello', [HelloController::class, 'hello']);

    $app->get('/invoices', [InvoiceController::class, 'index']);
    $app->get('/invoices/create', [InvoiceController::class, 'create']);
    $app->post('/invoices', [InvoiceController::class, 'store']);

    $app->get('/customers', [CustomerController::class, 'index']);
    $app->get('/customers/create', [CustomerController::class, 'create']);
    $app->post('/customers', [CustomerController::class, 'store']);
    
    $app->get('/api/customers/search', [CustomerApiController::class, 'search']);

    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
