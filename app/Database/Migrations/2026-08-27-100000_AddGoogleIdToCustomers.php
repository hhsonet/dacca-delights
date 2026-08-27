<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Links a customer to their Google account.
 *
 * Stored as the Google subject id rather than the email, because an email can
 * be reassigned or changed while the subject id is stable for the life of the
 * account.
 */
class AddGoogleIdToCustomers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('customers', [
            'google_id' => [
                'type' => 'VARCHAR', 'constraint' => 40, 'null' => true, 'after' => 'password_hash',
            ],
            'avatar_url' => [
                'type' => 'VARCHAR', 'constraint' => 300, 'null' => true, 'after' => 'google_id',
            ],
        ]);

        $this->db->query('ALTER TABLE customers ADD UNIQUE KEY customers_google_id_unique (google_id)');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE customers DROP INDEX customers_google_id_unique');
        $this->forge->dropColumn('customers', ['google_id', 'avatar_url']);
    }
}
