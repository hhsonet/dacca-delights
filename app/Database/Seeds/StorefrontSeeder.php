<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds the storefront tables from the catalogue the front-end already ships
 * (app/Database/Seeds/data/storefront-data.json, extracted from the storefront
 * logic) so the database mirrors what the site actually sells.
 */
class StorefrontSeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(__DIR__ . '/data/storefront-data.json');
        $d    = json_decode($json, true);

        if (!is_array($d)) {
            throw new \RuntimeException('Could not read storefront-data.json');
        }

        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // Truncation order respects the foreign keys.
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['order_items', 'orders', 'customers', 'products', 'categories',
                  'delivery_zones', 'testimonials', 'gallery'] as $t) {
            $db->table($t)->truncate();
        }
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        // ---- categories -------------------------------------------------
        // "Best Sellers" is a virtual filter, not a real category, so it is
        // derived from is_featured rather than stored.
        $catOrder = array_values(array_filter($d['CATS'], static fn($c) => $c !== 'Best Sellers'));
        $usedCats = array_values(array_unique(array_column($d['PRODUCTS'], 'cat')));

        $catIds = [];
        $sort   = 0;
        foreach ($catOrder as $name) {
            if (!in_array($name, $usedCats, true)) {
                continue;
            }
            $meta = $d['CAT_META'][$name] ?? [];
            $db->table('categories')->insert([
                'name'       => $name,
                'slug'       => $this->slugify($name),
                'blurb'      => $meta['blurb'] ?? null,
                'image'      => $meta['image'] ?? null,
                'sort_order' => $sort++,
            ]);
            $catIds[$name] = (int) $db->insertID();
        }

        // ---- products ----------------------------------------------------
        $itemMoq  = $d['ITEM_MOQ'] ?? [];
        $featured = $d['FEATURED'] ?? [];
        $rows     = [];

        foreach ($d['PRODUCTS'] as $p) {
            $name = $p['name'];
            $cat  = $p['cat'];

            // Bagels share one pooled minimum unless they carry their own MOQ
            // or are a pre-set bunch.
            $pooled = $cat === 'Bagels'
                && !preg_match('/bunch/i', $name)
                && !isset($itemMoq[$name]);

            $rows[] = [
                'category_id'   => $catIds[$cat] ?? null,
                'slug'          => $p['slug'],
                'name'          => $name,
                'note'          => $p['note'] ?? null,
                'price'         => (int) $p['price'],
                'kcal'          => isset($p['kcal']) ? (int) $p['kcal'] : null,
                'ingredients'   => $p['ing'] ?? null,
                'image'         => $p['image'] ?? null,
                'is_new'        => !empty($p['isNew']) ? 1 : 0,
                'is_featured'   => in_array($name, $featured, true) ? 1 : 0,
                'min_qty'       => (int) ($itemMoq[$name] ?? 1),
                'in_bagel_pool' => $pooled ? 1 : 0,
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }
        $db->table('products')->insertBatch($rows);

        // Map product name -> id so order lines can be linked.
        $productIds = [];
        foreach ($db->table('products')->select('id, name')->get()->getResultArray() as $r) {
            $productIds[$r['name']] = (int) $r['id'];
        }

        // ---- delivery zones ------------------------------------------------
        $cod   = $d['COD_ZONES'] ?? [];
        $zRows = [];
        foreach ($d['ZONES'] as $z) {
            $zRows[] = [
                'name'        => $z['name'],
                'fee'         => $z['fee'] === null ? null : (int) $z['fee'],
                'is_limited'  => !empty($z['limited']) ? 1 : 0,
                'cod_allowed' => in_array($z['name'], $cod, true) ? 1 : 0,
            ];
        }
        $db->table('delivery_zones')->insertBatch($zRows);

        $zoneIds = [];
        foreach ($db->table('delivery_zones')->select('id, name')->get()->getResultArray() as $r) {
            $zoneIds[$r['name']] = (int) $r['id'];
        }

        // ---- customers -------------------------------------------------------
        $customers = [
            ['first_name' => 'Hasan',  'last_name' => 'Sonet',     'email' => 'hasan.sonet@example.com',  'phone' => '+8801712345678', 'whatsapp' => '+8801712345678'],
            ['first_name' => 'Sarah',  'last_name' => 'Ahmed',     'email' => 'sarah.ahmed@example.com',  'phone' => '+8801811223344', 'whatsapp' => '+8801811223344'],
            ['first_name' => 'Adnan',  'last_name' => 'Chowdhury', 'email' => 'adnan.c@example.com',      'phone' => '+8801915556677', 'whatsapp' => '+8801915556677'],
            ['first_name' => 'Nusrat', 'last_name' => 'Jahan',     'email' => 'nusrat.jahan@example.com', 'phone' => '+8801677889900', 'whatsapp' => '+8801677889900'],
        ];
        foreach ($customers as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }
        unset($c);
        $db->table('customers')->insertBatch($customers);

        $customerIds = array_column(
            $db->table('customers')->select('id')->get()->getResultArray(),
            'id'
        );

        // ---- orders + order items ---------------------------------------------
        $zoneNames  = array_keys($zoneIds);
        $codZoneIds = array_values(array_intersect_key($zoneIds, array_flip($cod)));

        foreach ($d['ORDERS'] as $i => $o) {
            $subtotal = 0;
            foreach ($o['items'] as $line) {
                $subtotal += ((int) $line[1]) * ((int) $line[2]);
            }

            $discount = (int) ($o['discount'] ?? 0);
            $isCod    = stripos($o['payment'], 'cash') !== false;

            // Cash orders must sit in a COD-eligible zone; others spread around.
            $zoneId = $isCod
                ? ($codZoneIds[$i % max(1, count($codZoneIds))] ?? null)
                : ($zoneIds[$zoneNames[($i * 7) % count($zoneNames)]] ?? null);

            $fee      = (int) ($db->table('delivery_zones')->select('fee')->where('id', $zoneId)->get()->getRowArray()['fee'] ?? 0);
            $placedOn = date('Y-m-d', strtotime($o['date']));

            $db->table('orders')->insert([
                'order_no'       => ltrim($o['no'], '#'),
                'customer_id'    => $customerIds[$i % count($customerIds)],
                'zone_id'        => $zoneId,
                'placed_on'      => $placedOn,
                'delivery_date'  => date('Y-m-d', strtotime($placedOn . ' +1 day')),
                'is_pickup'      => 0,
                'payment_method' => $o['payment'],
                'payment_status' => $isCod ? 'Due on delivery' : 'Paid',
                'status'         => $o['status'],
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'delivery_fee'   => $fee,
                'total'          => $subtotal - $discount + $fee,
                'address'        => $o['address'] ?? null,
                'notes'          => $o['notes'] ?? null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            $orderId = (int) $db->insertID();
            $lines   = [];

            foreach ($o['items'] as $line) {
                [$pname, $qty, $price] = [$line[0], (int) $line[1], (int) $line[2]];
                $lines[] = [
                    'order_id'     => $orderId,
                    'product_id'   => $productIds[$pname] ?? null,
                    'product_name' => $pname,
                    'qty'          => $qty,
                    'unit_price'   => $price,
                    'line_total'   => $qty * $price,
                    'options'      => null,
                ];
            }
            $db->table('order_items')->insertBatch($lines);
        }

        // ---- testimonials ---------------------------------------------------------
        $tRows = [];
        foreach ($d['TESTIMONIALS'] as $t) {
            $tRows[] = [
                'name'         => $t['name'],
                // "★★★★★" -> 5
                'stars'        => mb_strlen(trim($t['stars'] ?? '★★★★★')),
                'quote'        => $t['quote'],
                'item'         => $t['item'] ?? null,
                'is_published' => 1,
                'created_at'   => $now,
            ];
        }
        $db->table('testimonials')->insertBatch($tRows);

        // ---- gallery ----------------------------------------------------------------
        $gRows = [];
        foreach ($d['GALLERY'] as $i => $g) {
            $gRows[] = [
                'src'        => $g['src'],
                'alt'        => $g['alt'] ?? null,
                'span'       => (int) ($g['span'] ?? 1),
                'sort_order' => $i,
            ];
        }
        $db->table('gallery')->insertBatch($gRows);
    }

    private function slugify(string $n): string
    {
        $s = strtolower($n);
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);

        return trim($s, '-');
    }
}
