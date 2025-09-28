<?php
namespace App\Controllers;

use App\Repositories\CustomerRepository;
use App\Repositories\InvoiceRepository;
use App\Support\FlashSession;
use Mpdf\Mpdf;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Slim\Views\Twig;

class InvoiceController extends BaseController
{

    protected Twig $view;
    protected InvoiceRepository $invoiceRepository;
    protected CustomerRepository $customerRepository;

    public function __construct(Twig $view, InvoiceRepository $invoiceRepository, CustomerRepository $customerRepository
    ) {
        $this->view               = $view;
        $this->invoiceRepository  = $invoiceRepository;
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request, Response $response): Response
    {
        $searchParams = $request->getQueryParams();
        $invoices     = $this->invoiceRepository->getFiltered($searchParams);

        return $this->view->render($response, 'invoices/index.twig', [
            'invoices'     => $invoices,
            'searchParams' => $searchParams,
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'invoices/create.twig');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // バリデーション処理
        $validator = Validator::
            key('customer_id', Validator::intVal()->positive()->setTemplate('顧客を選択してください'))
            ->key('title', Validator::stringVal()->length(1, 50)->setTemplate('タイトルは50文字以内にしてください'))
            ->key('amount', Validator::number()->min(0)->setTemplate('金額は0以上にしてください'))
            ->key('amount', Validator::number()->max(1000000)->setTemplate('金額は1,000,000以下にしてください'))
            ->key('invoice_date', Validator::date('Y-m-d')->setTemplate('請求日は日付形式にしてください'))
            ->key('note', Validator::stringVal()->length(0, 200)->setTemplate('備考は200文字以内にしてください'));
        try {
            $validator->assert($data);
        } catch (NestedValidationException $e) {
            // バリデーションエラー時
            FlashSession::set('errors', $e->getMessages());
            FlashSession::set('old', $data);
            return $response->withHeader('Location', $this->url('invoices/create'))->withStatus(302);
        }

        // 保存処理
        $this->invoiceRepository->save($data);

        FlashSession::set('successMessage', '請求書を登録しました');
        return $response
            ->withHeader('Location', $this->url('invoices'))
            ->withStatus(302);
    }
    /**
     * 請求書一覧を PDF で出力
     */
    public function exportPdf(Request $request, Response $response): Response
    {
        // 検索条件を取得（例: 顧客名）
        $searchParams = $request->getQueryParams();

        // データ取得（検索条件を使う）
        $invoices = $this->invoiceRepository->getFiltered($searchParams);

        // 合計を計算
        $total = 0;
        foreach ($invoices as $invoice) {
            $total += $invoice['amount'];
        }

        $customer = $this->customerRepository->find($searchParams['customer_id']);

        // Twig で HTML をレンダリング
        $html = $this->view->fetch('pdf/invoice.twig', [
            'invoices' => $invoices,
            'customer' => $customer,
            'total'    => $total,
        ]);

        // PDF 作成
        $mpdf = new Mpdf([
            'mode'   => 'ja',
            'format' => 'A4',
        ]);
        $mpdf->WriteHTML($html);
        $pdfContent = $mpdf->Output('', 'S'); // 'S' = 文字列として取得

        // レスポンスに PDF を書き込む
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $pdfContent);
        rewind($stream);

        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', 'inline; filename="invoices.pdf"')
            ->withBody(new \Slim\Psr7\Stream($stream));
    }
}
