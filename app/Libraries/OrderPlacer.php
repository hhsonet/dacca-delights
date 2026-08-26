<?php

namespace App\Libraries;

use App\Models\CustomerModel;
use App\Models\DeliveryZoneModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;

/**
 * Turns a submitted checkout into a stored order.
 *
 * SECURITY: nothing about money comes from the browser. Prices, minimums,
 * delivery fees and the discount are all re-read or re-derived from the
 * database here. A tampered payload can change *what* is ordered, never what
 * it costs.
 */
class OrderPlacer
{
    /** Bagels sold singly share one pooled minimum. Mirrors the client rule. */
    public const BAGEL_POOL_MOQ = 6;

    /** 20% off. The only coupon the storefront recognises. */
    public const COUPON_CODE    = 'SWEET20';
    public const COUPON_PERCENT = 20;

    public const BOOKING_WINDOW_DAYS = 30;

    /**
     * Breads sold as-is: no sugar/format choice.
     * These lists mirror the storefront's constants; they belong in the
     * products table eventually, but duplicating them is better than trusting
     * the client to tell us which options were required.
     */
    private const NO_BREAD_OPTIONS = [
        'Regular Ciabatta', 'White Ciabatta', 'Mini Baguette', 'Khobus',
        'Bánh Mì (Hoagie Bread)', 'Muffuletta Sandwich Bread', 'Simit',
        'Plain Focaccia', 'Mixed Herb Focaccia',
    ];
    private const SUGAR_ONLY = ['Jerusalem Bagel'];
    private const FILLINGS   = ['Chicken Puff', 'Mini Chicken Puff'];

    /** @var string[] */
    private array $errors = [];

    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return array|null The stored order (view-model shape), or null on failure.
     */
    public function place(array $in): ?array
    {
        $lines = $this->resolveLines($in['cart'] ?? []);
        if ($lines === null) {
            return null;
        }

        $pickup = !empty($in['pickup']);
        $zone   = $pickup ? null : $this->resolveZone((string) ($in['zone'] ?? ''));
        if (!$pickup && $zone === null) {
            return null;
        }

        $payment = (string) ($in['payment'] ?? '');
        if (!in_array($payment, ['cod', 'bkash', 'card'], true)) {
            $this->errors[] = 'Choose a payment method.';
        } elseif ($payment === 'cod' && !$pickup && empty($zone['cod_allowed'])) {
            // Otherwise a customer could pick cash in an area we do not collect it.
            $this->errors[] = 'Cash on delivery is not available for that area.';
        }

        $deliveryDate = $this->resolveDate((string) ($in['deliveryDate'] ?? ''));
        $contact      = $this->resolveContact($in, $pickup);

        $this->checkMinimums($lines);

        if ($this->errors !== []) {
            return null;
        }

        // ---- money: derived here, never accepted from the client ----------
        $subtotal = 0;
        foreach ($lines as $l) {
            $subtotal += $l['unit_price'] * $l['qty'];
        }

        $couponOk = strtoupper(trim((string) ($in['coupon'] ?? ''))) === self::COUPON_CODE;
        $discount = $couponOk ? (int) floor($subtotal * self::COUPON_PERCENT / 100) : 0;

        // No free-delivery threshold is in force on the storefront, so the fee
        // is simply the zone fee (or nothing for self-pickup).
        $deliveryFee = $pickup ? 0 : (int) $zone['fee'];
        $total       = $subtotal - $discount + $deliveryFee;

        return $this->persist($in, $lines, $contact, $zone, $pickup, $payment,
            $deliveryDate, $subtotal, $discount, $deliveryFee, $total);
    }

    // ------------------------------------------------------------------

    /** Re-reads every cart line against the database. */
    private function resolveLines($cart): ?array
    {
        if (!is_array($cart) || $cart === []) {
            $this->errors[] = 'Your cart is empty.';

            return null;
        }

        $model = new ProductModel();
        $lines = [];

        foreach ($cart as $item) {
            if (!is_array($item)) {
                continue;
            }
            $qty = (int) ($item['qty'] ?? 0);
            $id  = (int) ($item['id'] ?? 0);
            if ($qty < 1 || $id < 1) {
                continue;
            }
            if ($qty > 500) {
                $this->errors[] = 'That quantity is not available.';

                return null;
            }

            $p = $model->find($id);
            if (!$p || empty($p['is_active'])) {
                $this->errors[] = 'One of the items is no longer available.';

                return null;
            }

            $opts = [
                'filling' => $this->cleanOpt($item['filling'] ?? ''),
                'sugar'   => $this->cleanOpt($item['sugar'] ?? ''),
                'form'    => $this->cleanOpt($item['form'] ?? ''),
                'slice'   => $this->cleanOpt($item['slice'] ?? ''),
            ];

            if ($err = $this->optionProblem($p, $opts)) {
                $this->errors[] = $err;

                return null;
            }

            $lines[] = [
                'product_id'  => (int) $p['id'],
                'name'        => $p['name'],
                'qty'         => $qty,
                'unit_price'  => (int) $p['price'],   // from the DB, not the client
                'min_qty'     => max(1, (int) $p['min_qty']),
                'in_pool'     => (bool) $p['in_bagel_pool'],
                'options'     => implode(' · ', array_filter($opts)),
                'note'        => mb_substr(trim((string) ($item['note'] ?? '')), 0, 240),
            ];
        }

        if ($lines === []) {
            $this->errors[] = 'Your cart is empty.';

            return null;
        }

        return $lines;
    }

    private function cleanOpt($v): string
    {
        return mb_substr(trim((string) $v), 0, 40);
    }

    /** Mandatory choices must be present, and must be present legitimately. */
    private function optionProblem(array $p, array $opts): ?string
    {
        $name = $p['name'];

        if (in_array($name, self::FILLINGS, true) && $opts['filling'] === '') {
            return $name . ' needs a filling choice.';
        }

        $needsSugar = ($this->categoryName($p) === 'Breads' && !in_array($name, self::NO_BREAD_OPTIONS, true))
            || in_array($name, self::SUGAR_ONLY, true);

        if ($needsSugar) {
            if ($opts['sugar'] === '') {
                return $name . ' needs a sugar choice.';
            }
            $needsFormat = $this->categoryName($p) === 'Breads'
                && !in_array($name, self::NO_BREAD_OPTIONS, true);
            if ($needsFormat) {
                if ($opts['form'] === '') {
                    return $name . ' needs a format choice.';
                }
                if ($opts['form'] === 'Sliced' && $opts['slice'] === '') {
                    return $name . ' needs a slice thickness.';
                }
            }
        }

        return null;
    }

    private array $catCache = [];

    private function categoryName(array $p): string
    {
        $cid = (int) $p['category_id'];
        if (!isset($this->catCache[$cid])) {
            $row                   = (new \App\Models\CategoryModel())->find($cid);
            $this->catCache[$cid] = $row['name'] ?? '';
        }

        return $this->catCache[$cid];
    }

    /** Per-item minimums plus the shared bagel pool. */
    private function checkMinimums(array $lines): void
    {
        $poolQty = 0;
        foreach ($lines as $l) {
            if ($l['in_pool']) {
                $poolQty += $l['qty'];
            } elseif ($l['qty'] < $l['min_qty']) {
                $this->errors[] = $l['name'] . ' has a minimum of ' . $l['min_qty'] . '.';
            }
        }

        if ($poolQty > 0 && $poolQty < self::BAGEL_POOL_MOQ) {
            $this->errors[] = 'Bagels come in batches of ' . self::BAGEL_POOL_MOQ
                . ' — add ' . (self::BAGEL_POOL_MOQ - $poolQty) . ' more.';
        }
    }

    private function resolveZone(string $name): ?array
    {
        $zone = (new DeliveryZoneModel())->where('name', $name)->first();

        if (!$zone) {
            $this->errors[] = 'Choose a delivery area.';

            return null;
        }
        if ($zone['fee'] === null) {
            $this->errors[] = 'We do not deliver to that area yet.';

            return null;
        }

        return $zone;
    }

    private function resolveDate(string $raw): string
    {
        $ts = strtotime($raw);
        if (!$ts) {
            $this->errors[] = 'Choose a delivery date.';

            return '';
        }

        $date  = date('Y-m-d', $ts);
        $today = date('Y-m-d');
        $limit = date('Y-m-d', strtotime('+' . self::BOOKING_WINDOW_DAYS . ' days'));

        if ($date < $today) {
            $this->errors[] = 'That delivery date has already passed.';
        } elseif ($date > $limit) {
            $this->errors[] = 'Please choose a date within ' . self::BOOKING_WINDOW_DAYS . ' days.';
        }

        return $date;
    }

    private function resolveContact(array $in, bool $pickup): array
    {
        $first = trim((string) ($in['firstName'] ?? ''));
        $last  = trim((string) ($in['lastName'] ?? ''));
        $phone = preg_replace('/\D/', '', (string) ($in['localPhone'] ?? ''));

        if (mb_strlen($first) < 2 || mb_strlen($last) < 2) {
            $this->errors[] = 'Enter your first and last name.';
        }
        if (!preg_match('/^1[3-9]\d{8}$/', $phone)) {
            $this->errors[] = 'Enter a valid phone number.';
        }

        $waSame   = !empty($in['waSame']);
        $whatsapp = $waSame
            ? '+880' . $phone
            : trim((string) ($in['waCode'] ?? '')) . ' ' . preg_replace('/\D/', '', (string) ($in['waNumber'] ?? ''));

        if (!$waSame && strlen(preg_replace('/\D/', '', $whatsapp)) < 6) {
            $this->errors[] = 'Check your WhatsApp number.';
        }

        $address = '';
        if ($pickup) {
            $address = 'Self-pickup';
        } else {
            $house = trim((string) ($in['house'] ?? ''));
            $line1 = trim((string) ($in['line1'] ?? ''));
            if ($house === '' || mb_strlen($line1) < 2) {
                $this->errors[] = 'Complete the delivery address.';
            }
            $address = implode(', ', array_filter([
                $house, $line1,
                trim((string) ($in['line2'] ?? '')),
                trim((string) ($in['zone'] ?? '')),
                ($zip = trim((string) ($in['zip'] ?? ''))) !== '' ? 'Dhaka ' . $zip : '',
            ]));
        }

        // A signed-in customer's email is worth recording on the order too, so
        // the receipt and the dashboard have a way to reach them.
        $email = (string) (session()->get('customerEmail') ?? '');

        return [
            'first'    => mb_substr($first, 0, 80),
            'last'     => mb_substr($last, 0, 80),
            'name'     => mb_substr(trim($first . ' ' . $last), 0, 160),
            'phone'    => '+880' . $phone,
            'whatsapp' => mb_substr($whatsapp, 0, 32),
            'email'    => filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '',
            'address'  => mb_substr($address, 0, 500),
            'mapsUrl'  => mb_substr(trim((string) ($in['mapsUrl'] ?? '')), 0, 500),
        ];
    }

    /** Writes the order and its lines in one transaction. */
    private function persist(
        array $in, array $lines, array $contact, ?array $zone, bool $pickup,
        string $payment, string $deliveryDate,
        int $subtotal, int $discount, int $deliveryFee, int $total
    ): ?array {
        $orders = new OrderModel();
        $db     = $orders->db;

        $customerId = $this->resolveCustomer($contact);

        $payLabel = match ($payment) {
            'cod'   => $pickup ? 'Cash on pickup' : 'Cash on delivery',
            'bkash' => 'bKash',
            default => 'Card',
        };
        $payStatus = $payment === 'cod'
            ? ($pickup ? 'Due on pickup' : 'Due on delivery')
            : 'Paid';

        $db->transBegin();

        try {
            // order_no is set after insert so it can be derived from the real
            // primary key — guaranteed unique, unlike a random number.
            $orderId = $orders->protect(false)->insert([
                'order_no'       => 'TMP-' . bin2hex(random_bytes(6)),
                'customer_id'    => $customerId,
                'zone_id'        => $zone['id'] ?? null,
                'placed_on'      => date('Y-m-d'),
                'delivery_date'  => $deliveryDate,
                'is_pickup'      => $pickup ? 1 : 0,
                'payment_method' => $payLabel,
                'payment_status' => $payStatus,
                'status'         => 'Order Placed',
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'delivery_fee'   => $deliveryFee,
                'total'          => $total,
                // Snapshot of who ordered and how to reach them, as given at
                // checkout. Kept on the order so later profile edits cannot
                // rewrite history, and so guest orders survive without an account.
                'customer_name'     => $contact['name'],
                'customer_phone'    => $contact['phone'],
                'customer_whatsapp' => $contact['whatsapp'],
                'customer_email'    => $contact['email'] ?: null,
                'address'           => $contact['address'],
                'map_url'           => $contact['mapsUrl'] ?: null,
                'notes'             => mb_substr(trim((string) ($in['notes'] ?? '')), 0, 500) ?: null,
            ], true);

            if (!$orderId) {
                throw new \RuntimeException(implode(' ', $orders->errors()) ?: 'Could not save the order.');
            }

            $orderNo = 'DD-' . (10000 + (int) $orderId);
            $db->table('orders')->where('id', $orderId)->update(['order_no' => $orderNo]);

            $itemRows = [];
            foreach ($lines as $l) {
                $itemRows[] = [
                    'order_id'     => $orderId,
                    'product_id'   => $l['product_id'],
                    'product_name' => $l['name'],
                    'qty'          => $l['qty'],
                    'unit_price'   => $l['unit_price'],
                    'line_total'   => $l['unit_price'] * $l['qty'],
                    'options'      => trim($l['options'] . ($l['note'] !== '' ? ' · Note: ' . $l['note'] : '')) ?: null,
                ];
            }
            (new OrderItemModel())->insertBatch($itemRows);

            $db->transCommit();
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'Order placement failed: ' . $e->getMessage());
            $this->errors[] = 'We could not save your order. Please try again.';

            return null;
        }

        return [
            'orderNo'       => $orderNo,
            'placedOn'      => date('j M Y'),
            'placedTime'    => date('g:i A'),
            'deliveryDate'  => date('l, j F Y', strtotime($deliveryDate)),
            'isPickup'      => $pickup,
            'zoneName'      => $pickup ? 'Self-pickup' : $zone['name'],
            'customer'      => trim($contact['first'] . ' ' . $contact['last']),
            'phone'         => $contact['phone'],
            'whatsapp'      => $contact['whatsapp'],
            'address'       => $contact['address'],
            'mapsUrl'       => $contact['mapsUrl'],
            'paymentLabel'  => $payLabel,
            'paymentStatus' => $payStatus,
            'items'         => array_map(static fn ($l) => [
                'name'      => $l['name'],
                'qty'       => $l['qty'],
                'options'   => $l['options'],
                'note'      => $l['note'],
                'lineTotal' => $l['unit_price'] * $l['qty'],
            ], $lines),
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'deliveryFee' => $deliveryFee,
            'total'       => $total,
        ];
    }

    /**
     * Signed-in customers keep their own record. Guests get (or reuse) a
     * password-less customer row so their order still has an owner.
     */
    private function resolveCustomer(array $contact): ?int
    {
        $model = new CustomerModel();

        if ($id = session()->get('customerId')) {
            return (int) $id;
        }

        // Only ever reuse a guest row. If the number belongs to a registered
        // account, do NOT attach — otherwise anyone typing that phone number at
        // checkout would have their order surface in that person's order
        // history. The order's own snapshot still records who placed it.
        $existing = $model->where('phone', $contact['phone'])
            ->where('password_hash IS NULL', null, false)
            ->first();

        if ($existing) {
            return (int) $existing['id'];
        }

        $registered = $model->where('phone', $contact['phone'])
            ->where('password_hash IS NOT NULL', null, false)
            ->first();

        if ($registered) {
            return null;
        }

        $id = $model->insert([
            'first_name' => $contact['first'],
            'last_name'  => $contact['last'],
            'phone'      => $contact['phone'],
            'whatsapp'   => $contact['whatsapp'],
        ], true);

        if (!$id) {
            // The order still carries its own contact snapshot, so a failure
            // here loses the account link but never the customer's details.
            log_message('error', 'Guest customer creation failed: ' . implode(' ', $model->errors()));

            return null;
        }

        return (int) $id;
    }
}
