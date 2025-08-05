<?php

namespace App\Repositories;

use PDO;

class InvoiceRepository {

    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll(): array {
        $stmt = $this->db->query("
        SELECT invoices.*, customers.company_name 
        FROM invoices 
        JOIN customers ON invoices.customer_id = customers.id
        ORDER BY invoices.invoice_date DESC
    ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM invoices WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
        return $invoice ?: null;
    }

    public function save(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO invoices (
                customer_id, invoice_date, due_date, total_amount, status, created_at, updated_at
            ) VALUES (
                :customer_id, :invoice_date, :due_date, :total_amount, :status, :created_at, :updated_at
            )
        ");

        $now = date('Y-m-d H:i:s');

        return $stmt->execute([
                    ':customer_id' => $data['customer_id'],
                    ':invoice_date' => $data['invoice_date'],
                    ':due_date' => $data['due_date'],
                    ':total_amount' => $data['total_amount'],
                    ':status' => 0,
                    ':created_at' => $now,
                    ':updated_at' => $now,
        ]);
    }
}
