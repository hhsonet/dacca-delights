<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Take the minimum out of the product note.
 *
 * Since min_qty was backfilled the minimum is stated by the "Min N" badge on
 * the cards and the callout on the product page, both read from data. Leaving
 * it in the note as well meant the same fact appeared three times in three
 * wordings, and the prose could not follow an edit to min_qty — change the
 * minimum in the admin and the note would keep advertising the old number.
 *
 * The note goes back to describing the item: "~90 gm", "choose spicy or
 * creamy". Where the whole note was the minimum, it is cleared, and the
 * product page falls back to the category blurb as it already did.
 */
class RemoveMinimumFromProductNote extends Migration
{
    public function up(): void
    {
        $rows = $this->db->table('products')
            ->select('id, note')
            ->like('note', 'minimum')
            ->get()->getResultArray();

        foreach ($rows as $r) {
            $note = (string) $r['note'];

            // Notes are "·"-separated clauses; drop only the clause that is
            // purely a minimum declaration and keep everything else.
            $parts = preg_split('/\s*·\s*/u', $note);
            $kept  = array_filter(
                $parts,
                static fn ($p) => !preg_match('/^\s*minimum\s+\d+\s*(pcs|pieces)?\s*$/iu', $p)
            );

            $clean = trim(implode(' · ', array_map('trim', $kept)));

            $this->db->table('products')
                ->where('id', $r['id'])
                ->update(['note' => $clean === '' ? null : $clean]);
        }
    }

    public function down(): void
    {
        // The removed wording is not recoverable — it was free text, and
        // nothing recorded it. The minimum itself is safe in min_qty, so
        // rolling back loses only the duplicated phrasing.
    }
}
