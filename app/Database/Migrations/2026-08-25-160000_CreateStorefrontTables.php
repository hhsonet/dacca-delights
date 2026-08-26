<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStorefrontTables extends Migration
{
    public function up(): void
    {
        // ---- categories -------------------------------------------------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 120],
            'blurb'      => ['type' => 'TEXT', 'null' => true],
            'image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('categories');

        // ---- products ---------------------------------------------------
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category_id' => ['type' => 'INT', 'unsigned' => true],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 160],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 160],
            'note'        => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            // Taka, whole units — no fractional currency in this catalogue.
            'price'       => ['type' => 'INT', 'unsigned' => true],
            'kcal'        => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'ingredients' => ['type' => 'TEXT', 'null' => true],
            'image'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_new'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_featured' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            // Minimum order quantity for this item on its own.
            'min_qty'     => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            // Bagels share one pooled minimum instead of a per-item one.
            'in_bagel_pool' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('category_id');
        $this->forge->addForeignKey('category_id', 'categories', 'id', '', 'CASCADE');
        $this->forge->createTable('products');

        // ---- delivery_zones ---------------------------------------------
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
            // NULL fee = area not served.
            'fee'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'is_limited'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'cod_allowed' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('delivery_zones');

        // ---- customers ---------------------------------------------------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => 80],
            'last_name'  => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'phone'      => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'whatsapp'   => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('customers');

        // ---- orders -------------------------------------------------------
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_no'      => ['type' => 'VARCHAR', 'constraint' => 32],
            'customer_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'zone_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'placed_on'     => ['type' => 'DATE', 'null' => true],
            'delivery_date' => ['type' => 'DATE', 'null' => true],
            'is_pickup'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'payment_status' => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'Order Placed'],
            'subtotal'      => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'discount'      => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'delivery_fee'  => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'total'         => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'address'       => ['type' => 'TEXT', 'null' => true],
            'notes'         => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_no');
        $this->forge->addKey('customer_id');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', '', 'SET NULL');
        $this->forge->addForeignKey('zone_id', 'delivery_zones', 'id', '', 'SET NULL');
        $this->forge->createTable('orders');

        // ---- order_items ---------------------------------------------------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'   => ['type' => 'INT', 'unsigned' => true],
            // Kept nullable so historic lines survive a product being removed.
            'product_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 160],
            'qty'        => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'unit_price' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'line_total' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            // Per-line choices: sugar, form, slice, filling, note.
            'options'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', '', 'SET NULL');
        $this->forge->createTable('order_items');

        // ---- testimonials ----------------------------------------------------
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 120],
            'stars'        => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 5],
            'quote'        => ['type' => 'TEXT'],
            'item'         => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'is_published' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('testimonials');

        // ---- gallery ----------------------------------------------------------
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'src'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'alt'        => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'span'       => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 1],
            'sort_order' => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('gallery');
    }

    public function down(): void
    {
        // Child tables first — foreign keys.
        $this->forge->dropTable('gallery', true);
        $this->forge->dropTable('testimonials', true);
        $this->forge->dropTable('order_items', true);
        $this->forge->dropTable('orders', true);
        $this->forge->dropTable('customers', true);
        $this->forge->dropTable('delivery_zones', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
    }
}
