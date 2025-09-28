<?php

declare (strict_types = 1);

use Phinx\Migration\AbstractMigration;

final class CreateCustomersTable extends AbstractMigration
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
        $table = $this->table('customers');
        $table->addColumn('company_name', 'string', ['limit' => 100])
            ->addColumn('contact_name', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('email', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('phone', 'string', ['limit' => 100, 'null' => true])
            ->addColumn('address', 'text', ['limit' => 100, 'null' => true])
            ->addTimestamps() // created_at と updated_at を追加
            ->create();
    }
}
