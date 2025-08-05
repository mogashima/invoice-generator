<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInvoicesTable extends AbstractMigration {

    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void {
        $table = $this->table('invoices');
        $table->addColumn('customer_id', 'integer') // 顧客ID（外部キー想定）
                ->addColumn('invoice_date', 'date')   // 請求日
                ->addColumn('due_date', 'date')       // 支払期限
                ->addColumn('total_amount', 'decimal', ['precision' => 10, 'scale' => 2]) // 合計金額
                ->addColumn('status', 'integer', ['default' => 0, 'signed' => false]) // 0〜7を想定
                ->addTimestamps() // created_at, updated_at
                ->create();
    }
}
