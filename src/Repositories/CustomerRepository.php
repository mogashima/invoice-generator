<?php
namespace App\Repositories;

use PDO;

class CustomerRepository
{

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM customers ORDER BY created_at desc");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO customers (
                company_name, contact_name, email, phone, address, created_at, updated_at
            ) VALUES (
                :company_name, :contact_name, :email, :phone, :address, :created_at, :updated_at
            )
        ");

        $now = date('Y-m-d H:i:s');

        return $stmt->execute([
            ':company_name' => $data['company_name'],
            ':contact_name' => $data['contact_name'],
            ':email'        => $data['email'],
            ':phone'        => $data['phone'],
            ':address'      => $data['address'],
            ':created_at'   => $now,
            ':updated_at'   => $now,
        ]);
    }

    public function find($id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        return $customer ?: null;
    }

    public function search(string $keyword): array
    {
        $stmt = $this->db->prepare("SELECT id, contact_name, company_name FROM customers WHERE contact_name LIKE :keyword1 OR company_name LIKE :keyword2 ORDER BY created_at DESC");
        $stmt->bindValue(':keyword1', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':keyword2', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
