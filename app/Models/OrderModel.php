<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table         = 'orders';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'order_no', 'customer_id', 'zone_id', 'placed_on', 'delivery_date', 'is_pickup',
        'payment_method', 'payment_status', 'status', 'subtotal', 'discount',
        'delivery_fee', 'total', 'address', 'map_url', 'notes',
        // Contact details as given at checkout — see the snapshot migration.
        'customer_name', 'customer_phone', 'customer_whatsapp', 'customer_email',
    ];

    protected $validationRules = [
        // Required so is_unique's {id} placeholder resolves on update.
        'id'       => 'permit_empty|is_natural_no_zero',
        'order_no' => 'required|max_length[32]|is_unique[orders.order_no,id,{id}]',
        'status'   => 'required|max_length[40]',
    ];

    /** The statuses the dashboard is allowed to set, in fulfilment order. */
    public const STATUSES = [
        'Pending', 'Order Placed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled',
    ];

    /**
     * The order's own contact snapshot wins; the joined profile is only a
     * fallback for rows predating that column.
     */
    public function withCustomer(): \CodeIgniter\Database\BaseBuilder
    {
        // All-or-nothing: an order that carries its own snapshot uses only its
        // own fields. Mixing them with the joined profile would show one
        // person's name beside another's email when a guest order is linked to
        // an existing customer row.
        $hasSnapshot = "(o.customer_name IS NOT NULL AND o.customer_name <> '')";

        return $this->db->table($this->table . ' o')
            ->select("o.*,
                CASE WHEN {$hasSnapshot} THEN o.customer_name
                     ELSE NULLIF(TRIM(CONCAT(COALESCE(c.first_name,''),' ',COALESCE(c.last_name,''))),'') END AS customer_name,
                CASE WHEN {$hasSnapshot} THEN o.customer_email ELSE c.email END AS customer_email,
                CASE WHEN {$hasSnapshot} THEN o.customer_phone ELSE c.phone END AS customer_phone,
                z.name AS zone_name", false)
            ->join('customers c', 'c.id = o.customer_id', 'left')
            ->join('delivery_zones z', 'z.id = o.zone_id', 'left');
    }

    public function findFull(int $id): ?array
    {
        $row = $this->withCustomer()->where('o.id', $id)->get()->getRowArray();

        return $row ?: null;
    }

    /** Headline figures for the dashboard landing page. */
    public function stats(): array
    {
        $db = $this->db;

        $revenueRow = $db->table($this->table)
            ->selectSum('total', 'revenue')
            ->where('status !=', 'Cancelled')
            ->get()->getRowArray();

        $byStatus = $db->table($this->table)
            ->select('status, COUNT(*) AS n')
            ->groupBy('status')
            ->get()->getResultArray();

        return [
            'orders'   => (int) $db->table($this->table)->countAllResults(),
            'revenue'  => (int) ($revenueRow['revenue'] ?? 0),
            'byStatus' => array_column($byStatus, 'n', 'status'),
        ];
    }

    /**
     * Units sold per product over a trailing window — this replaces the
     * hardcoded WEEKLY_SALES map the storefront used to rank best sellers.
     */
    public function unitsSold(int $days = 7): array
    {
        $rows = $this->db->table('order_items oi')
            ->select('oi.product_name, SUM(oi.qty) AS units')
            ->join('orders o', 'o.id = oi.order_id', 'inner')
            ->where('o.status !=', 'Cancelled')
            ->groupBy('oi.product_name')
            ->orderBy('units', 'DESC')
            ->get()->getResultArray();

        return array_map('intval', array_column($rows, 'units', 'product_name'));
    }
}
