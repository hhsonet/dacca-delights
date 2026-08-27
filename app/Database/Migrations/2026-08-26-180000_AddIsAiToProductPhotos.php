<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Marks whether a product photo is a real photograph or AI-generated.
 *
 * Set by staff in the dashboard, shown to customers as a small badge so an
 * illustrative render is never mistaken for a photo of the actual bake.
 */
class AddIsAiToProductPhotos extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('product_photos', [
            'is_ai' => [
                'type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'filename',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('product_photos', 'is_ai');
    }
}
