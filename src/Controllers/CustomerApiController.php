<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\CustomerRepository;

class CustomerApiController {

    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository) {
        $this->customerRepository = $customerRepository;
    }

    public function search(Request $request, Response $response): Response {
        $params = $request->getQueryParams();
        $keyword = isset($params['keyword']) ? $params['keyword'] : '';
        $customers = $this->customerRepository->search($keyword);
        $response->getBody()->write(json_encode($customers));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
