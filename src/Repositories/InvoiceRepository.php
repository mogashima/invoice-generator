<?php
namespace App\Repositories;

use PDO;

class InvoiceRepository
{

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
        SELECT invoices.*, customers.company_name
        FROM invoices
        JOIN customers ON invoices.customer_id = customers.id
        ORDER BY invoices.invoice_date DESC
    ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function getFiltered(array $params): array
    {
        $sql = "
        SELECT invoices.*, customers.company_name
        FROM invoices
        JOIN customers ON invoices.customer_id = customers.id
        WHERE 1
    ";
        $bindings = [];

        // 顧客IDで絞り込み
        if (! empty($params['customer_id'])) {
            $sql .= " AND invoices.customer_id = :customer_id";
            $bindings[':customer_id'] = $params['customer_id'];
        }

        // 請求日の開始日で絞り込み
        if (! empty($params['from_date'])) {
            $sql .= " AND invoices.invoice_date >= :from_date";
            $bindings[':from_date'] = $params['from_date'];
        }

        // 請求日の終了日で絞り込み
        if (! empty($params['to_date'])) {
            $sql .= " AND invoices.invoice_date <= :to_date";
            $bindings[':to_date'] = $params['to_date'];
        }

        $sql .= " ORDER BY invoices.invoice_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM invoices WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        return $invoice ?: null;
    }

    public function save(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO invoices (
                customer_id, invoice_date, title, amount, status, note, created_at, updated_at
            ) VALUES (
                :customer_id, :invoice_date, :title, :amount, :status, :note, :created_at, :updated_at
            )
        ");

        $now = date('Y-m-d H:i:s');

        return $stmt->execute([
            ':customer_id'  => $data['customer_id'],
            ':invoice_date' => $data['invoice_date'],
            ':title'        => $data['title'],
            ':amount'       => $data['amount'],
            ':status'       => 0,
            ':note'         => $data['note'],
            ':created_at'   => $now,
            ':updated_at'   => $now,
        ]);
    }
}
