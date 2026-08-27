<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductPhotoModel extends Model
{
    protected $table         = 'product_photos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['product_id', 'filename', 'is_ai', 'sort_order', 'created_at'];

    /** Photos for one product, in display order. */
    public function forProduct(int $productId): array
    {
        return $this->where('product_id', $productId)
            ->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')
            ->findAll();
    }

    /** All photos grouped by product id, in display order. */
    public function allByProduct(): array
    {
        $rows = $this->db->query(
            'SELECT p.product_id, p.filename, p.is_ai, pr.code
             FROM product_photos p
             JOIN products pr ON pr.id = p.product_id
             ORDER BY p.product_id ASC, p.sort_order ASC, p.id ASC'
        )->getResultArray();

        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r['product_id']][] = [
                'path'  => $r['code'] . '/' . $r['filename'],
                'is_ai' => (bool) $r['is_ai'],
            ];
        }

        return $out;
    }

    /** First photo per product, keyed by product id — for listings. */
    public function primaryByProduct(): array
    {
        $rows = $this->db->query(
            'SELECT p.product_id, p.filename, p.is_ai, pr.code
             FROM product_photos p
             JOIN products pr ON pr.id = p.product_id
             WHERE p.id = (
                 SELECT id FROM product_photos
                 WHERE product_id = p.product_id
                 ORDER BY sort_order ASC, id ASC LIMIT 1
             )'
        )->getResultArray();

        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r['product_id']] = [
                'path'  => $r['code'] . '/' . $r['filename'],
                'is_ai' => (bool) $r['is_ai'],
            ];
        }

        return $out;
    }
}
