<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Staff logins, deliberately separate from `customers`.
 *
 * Shoppers and staff never share a credential store or a session key, so a
 * compromised customer account cannot be escalated into the dashboard.
 */
class CreateAdminUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'          => ['type' => 'VARCHAR', 'constraint' => 120],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 160],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'          => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'staff'],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('admin_users');
    }

    public function down(): void
    {
        $this->forge->dropTable('admin_users', true);
    }
}
