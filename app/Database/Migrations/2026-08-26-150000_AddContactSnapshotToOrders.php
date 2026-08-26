<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Snapshot the contact details onto each order.
 *
 * Previously an order only pointed at a `customers` row, so editing a profile
 * silently rewrote the history of every past order — and guest orders, which
 * reuse a customer row matched on phone, could overwrite each other's name.
 *
 * An order is a record of what was agreed at the time, so it now carries its
 * own copy. `customer_id` still links to the account for "my orders" lookups.
 */
class AddContactSnapshotToOrders extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('orders', [
            'customer_name' => [
                'type' => 'VARCHAR', 'constraint' => 160, 'null' => true, 'after' => 'customer_id',
            ],
            'customer_phone' => [
                'type' => 'VARCHAR', 'constraint' => 32, 'null' => true, 'after' => 'customer_name',
            ],
            'customer_whatsapp' => [
                'type' => 'VARCHAR', 'constraint' => 32, 'null' => true, 'after' => 'customer_phone',
            ],
            'customer_email' => [
                'type' => 'VARCHAR', 'constraint' => 160, 'null' => true, 'after' => 'customer_whatsapp',
            ],
            // Kept out of `address` so it can be linked rather than read.
            'map_url' => [
                'type' => 'VARCHAR', 'constraint' => 500, 'null' => true, 'after' => 'address',
            ],
        ]);

        // Backfill existing orders from the customer they point at, so nothing
        // renders blank, and strip the map link previously appended to address.
        $db = $this->db;

        $db->query(
            'UPDATE orders o
             JOIN customers c ON c.id = o.customer_id
             SET o.customer_name  = TRIM(CONCAT(COALESCE(c.first_name,""), " ", COALESCE(c.last_name,""))),
                 o.customer_phone = c.phone,
                 o.customer_whatsapp = c.whatsapp,
                 o.customer_email = c.email
             WHERE o.customer_name IS NULL'
        );

        $db->query(
            'UPDATE orders
             SET map_url = TRIM(SUBSTRING(address, LOCATE(" | Map: ", address) + 8)),
                 address = TRIM(SUBSTRING(address, 1, LOCATE(" | Map: ", address) - 1))
             WHERE address LIKE "% | Map: %"'
        );
    }

    public function down(): void
    {
        $this->forge->dropColumn('orders', [
            'customer_name', 'customer_phone', 'customer_whatsapp', 'customer_email', 'map_url',
        ]);
    }
}
