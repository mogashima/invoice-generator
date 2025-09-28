<?php
namespace App\Controllers;

use App\Repositories\CustomerRepository;
use App\Support\FlashSession;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Views\Twig;

class CustomerController extends BaseController
{

    protected Twig $view;
    protected CustomerRepository $customerRepository;

    public function __construct(Twig $view, CustomerRepository $customerRepository)
    {
        $this->view               = $view;
        $this->customerRepository = $customerRepository;
    }

    // 一覧表示 (GET /customers)
    public function index(Request $request, Response $response): Response
    {
        $customers = $this->customerRepository->getAll();
        return $this->view->render($response, 'customers/index.twig', [
            'customers' => $customers,
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'customers/create.twig');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // バリデーション処理
        $validator = Validator::
            key('company_name', Validator::stringVal()->length(1, 10)->setTemplate('会社名は10文字以内にしてください'))
            ->key('contact_name', Validator::stringVal()->length(1, 10)->setTemplate('担当者名は10文字以内にしてください'))
            ->key('email', Validator::stringVal()->length(1, 50)->setTemplate('メールアドレスは50文字以内にしてください'))
            ->key('address', Validator::stringVal()->length(0, 100)->setTemplate('住所は100文字以内にしてください'))
            ->key('phone', Validator::stringVal()->length(0, 20)->setTemplate('電話番号は20文字以内にしてください'));
        try {
            $validator->assert($data);
        } catch (NestedValidationException $e) {
            // バリデーションエラー時
            FlashSession::set('errors', $e->getMessages());
            FlashSession::set('old', $data);
            return $response->withHeader('Location', $this->url('customers/create'))->withStatus(302);
        }

        // 保存処理
        $this->customerRepository->save($data);

        FlashSession::set('successMessage', '顧客を登録しました');
        return $response
            ->withHeader('Location', $this->url('customers'))
            ->withStatus(302);
    }
}
