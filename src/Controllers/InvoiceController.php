<?php
namespace App\Controllers;

use App\Repositories\InvoiceRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class InvoiceController extends BaseController
{

    protected Twig $view;
    protected InvoiceRepository $invoiceRepository;

    public function __construct(Twig $view, InvoiceRepository $invoiceRepository)
    {
        $this->view              = $view;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function index(Request $request, Response $response): Response
    {
        $invoices = $this->invoiceRepository->getAll();

        return $this->view->render($response, 'invoices/index.twig', [
            'invoices' => $invoices,
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'invoices/create.twig');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // バリデーションと保存処理（必要に応じて追加）
        $this->invoiceRepository->save($data);

        return $response
            ->withHeader('Location', $this->url('invoices'))
            ->withStatus(302);
    }
}
