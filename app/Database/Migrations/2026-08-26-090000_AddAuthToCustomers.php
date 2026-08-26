<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Turns `customers` into the single identity for the storefront.
 *
 * Existing seeded rows keep password_hash = NULL, which means "guest checkout,
 * never registered" — those accounts cannot be logged into, and the login code
 * treats a NULL hash as a failed attempt rather than a bypass.
 */
class AddAuthToCustomers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('customers', [
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'whatsapp',
            ],
            'email_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'password_hash',
            ],
            'last_login_at' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'email_verified',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('customers', ['password_hash', 'email_verified', 'last_login_at']);
    }
}
