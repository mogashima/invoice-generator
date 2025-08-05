<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Repositories\CustomerRepository;

class CustomerController {

    protected Twig $view;
    protected CustomerRepository $customerRepository;

    public function __construct(Twig $view, CustomerRepository $customerRepository) {
        $this->view = $view;
        $this->customerRepository = $customerRepository;
    }

    // 一覧表示 (GET /customers)
    public function index(Request $request, Response $response): Response {
        $customers = $this->customerRepository->getAll();
        return $this->view->render($response, 'customers/index.twig', [
                    'customers' => $customers
        ]);
    }

    public function create(Request $request, Response $response): Response {
        return $this->view->render($response, 'customers/create.twig');
    }

    public function store(Request $request, Response $response): Response {
        $data = $request->getParsedBody();

        // バリデーションと保存処理（必要に応じて追加）
        $this->customerRepository->save($data);

        return $response
                        ->withHeader('Location', '/customers')
                        ->withStatus(302);
    }
}
