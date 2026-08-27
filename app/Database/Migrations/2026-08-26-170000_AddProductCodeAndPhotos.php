<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Gives every product a short human-quotable code and a place to keep uploaded
 * photos.
 *
 * Photos live on disk under public/uploads/products/<CODE>/ and are indexed
 * here so their order (and which one is the primary image) is stable rather
 * than depending on how the filesystem happens to list a directory.
 */
class AddProductCodeAndPhotos extends Migration
{
    /** Ambiguous characters (0/O/1/I/L) left out so codes survive being read aloud. */
    private const ALPHABET_LETTERS = 'ABCDEFGHJKMNPQRSTUVWXYZ';
    private const ALPHABET_DIGITS  = '23456789';

    public function up(): void
    {
        $this->forge->addColumn('products', [
            'code' => [
                'type' => 'VARCHAR', 'constraint' => 6, 'null' => true, 'after' => 'id',
            ],
        ]);

        // Backfill existing rows before the unique index goes on.
        $rows = $this->db->table('products')->select('id')->get()->getResultArray();
        $used = [];
        foreach ($rows as $r) {
            do {
                $code = $this->makeCode();
            } while (isset($used[$code]));
            $used[$code] = true;
            $this->db->table('products')->where('id', $r['id'])->update(['code' => $code]);
        }

        $this->db->query('ALTER TABLE products ADD UNIQUE KEY products_code_unique (code)');

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            // Filename only; the directory is derived from the product code.
            'filename'   => ['type' => 'VARCHAR', 'constraint' => 120],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['product_id', 'sort_order']);
        $this->forge->addForeignKey('product_id', 'products', 'id', '', 'CASCADE');
        $this->forge->createTable('product_photos');
    }

    public function down(): void
    {
        $this->forge->dropTable('product_photos', true);
        $this->db->query('ALTER TABLE products DROP INDEX products_code_unique');
        $this->forge->dropColumn('products', 'code');
    }

    /** 6 characters, guaranteed to contain at least one letter and one digit. */
    private function makeCode(): string
    {
        $letters = self::ALPHABET_LETTERS;
        $digits  = self::ALPHABET_DIGITS;
        $all     = $letters . $digits;

        $chars = [
            $letters[random_int(0, strlen($letters) - 1)],
            $digits[random_int(0, strlen($digits) - 1)],
        ];
        for ($i = 0; $i < 4; $i++) {
            $chars[] = $all[random_int(0, strlen($all) - 1)];
        }
        shuffle($chars);

        return implode('', $chars);
    }
}
