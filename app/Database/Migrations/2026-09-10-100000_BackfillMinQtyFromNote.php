<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Move the order minimums out of prose and into data.
 *
 * The catalogue advertised minimums in the product note — "minimum 4 pcs" —
 * but only four items ever had `min_qty` set. Fifty-three products claimed a
 * minimum and forty-nine of them were unenforced: the card said "minimum 4
 * pcs" and adding one put a single item in the cart, which checkout then
 * accepted because the server had nothing to check against either.
 *
 * The note stays as display copy; `min_qty` is the value everything else
 * reads, and the admin can edit it per product from here on.
 */
class BackfillMinQtyFromNote extends Migration
{
    /** Items whose minimum was already correct before this ran. */
    private const ALREADY_SET = [
        'Jerusalem Bagel', 'Cinnamon Raisin Bagel', 'Chicken Puff', 'Mini Chicken Puff',
    ];

    public function up(): void
    {
        $rows = $this->db->table('products')
            ->select('id, name, note, min_qty, in_bagel_pool')
            ->get()->getResultArray();

        foreach ($rows as $r) {
            if (!preg_match('/minimum\s+(\d+)/i', (string) $r['note'], $m)) {
                continue;
            }

            // Single bagels share one pooled minimum across flavours, so a
            // per-line minimum would stop someone ordering 2 of one and 4 of
            // another. That rule is enforced at cart level instead.
            if (!empty($r['in_bagel_pool'])) {
                continue;
            }

            $fromNote = (int) $m[1];
            $current  = (int) $r['min_qty'];

            // Never lower a minimum that is already stricter than the copy.
            if ($fromNote <= $current) {
                continue;
            }

            $this->db->table('products')
                ->where('id', $r['id'])
                ->update(['min_qty' => $fromNote]);
        }
    }

    public function down(): void
    {
        // Reset only what this migration could have raised, leaving the four
        // that were correct beforehand untouched.
        $rows = $this->db->table('products')
            ->select('id, name, note')
            ->get()->getResultArray();

        foreach ($rows as $r) {
            if (in_array($r['name'], self::ALREADY_SET, true)) {
                continue;
            }
            if (!preg_match('/minimum\s+\d+/i', (string) $r['note'])) {
                continue;
            }
            $this->db->table('products')->where('id', $r['id'])->update(['min_qty' => 1]);
        }
    }
}
