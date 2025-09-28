<?php

declare (strict_types = 1);

use Phinx\Migration\AbstractMigration;

final class CreateInvoicesTable extends AbstractMigration
{

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
    public function change(): void
    {
        $table = $this->table('invoices');
        $table->addColumn('customer_id', 'integer') // 顧客ID（外部キー）
            ->addColumn('title', 'string', ['limit' => 100])
            ->addColumn('invoice_date', 'date')
            ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 2])
            ->addColumn('status', 'integer', ['default' => 0, 'signed' => false])
            ->addColumn('note', 'string', ['limit' => 255])
            ->addTimestamps() // created_at, updated_at
            ->create();
    }
}
