<?php
// Everything the customer gave at checkout, as recorded on the order itself.
$details = [
    'Customer'   => $order['customer_name'] ?: '—',
    'Phone'      => $order['customer_phone'] ?: '—',
    'WhatsApp'   => $order['customer_whatsapp'] ?: '—',
    'Email'      => $order['customer_email'] ?: '—',
    'Placed'     => $order['placed_on'] ?? '—',
    'Delivery'   => $order['delivery_date'] ?? '—',
    'Fulfilment' => $order['is_pickup']
        ? 'Self-pickup'
        : 'Delivery' . ($order['zone_name'] ? ' · ' . $order['zone_name'] : ''),
    'Payment'    => ($order['payment_method'] ?? '—') . ' · ' . ($order['payment_status'] ?? '—'),
    'Address'    => $order['address'] ?: '—',
    'Notes'      => $order['notes'] ?: '—',
];
ob_start();
?>
<div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));align-items:start">

  <section class="card">
    <div class="hd"><h2>Items</h2></div>
    <div class="tablewrap"><table>
      <thead><tr><th>Product</th><th>Qty</th><th>Unit</th><th>Line</th></tr></thead>
      <tbody>
      <?php foreach ($items as $i): ?>
        <tr>
          <td><?= esc($i['product_name']) ?>
            <?php if (!empty($i['options'])): ?>
              <div class="muted" style="font-size:12px"><?= esc($i['options']) ?></div>
            <?php endif; ?></td>
          <td><?= (int) $i['qty'] ?></td>
          <td><?= number_format((int) $i['unit_price']) ?> tk</td>
          <td><strong><?= number_format((int) $i['line_total']) ?> tk</strong></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>

    <div style="padding:14px 16px;display:flex;flex-direction:column;gap:6px;font-size:13px">
      <div style="display:flex;justify-content:space-between">
        <span class="muted">Subtotal</span><span><?= number_format((int) $order['subtotal']) ?> tk</span></div>
      <?php if ((int) $order['discount'] > 0): ?>
        <div style="display:flex;justify-content:space-between">
          <span class="muted">Discount</span><span>−<?= number_format((int) $order['discount']) ?> tk</span></div>
      <?php endif; ?>
      <div style="display:flex;justify-content:space-between">
        <span class="muted">Delivery</span><span><?= number_format((int) $order['delivery_fee']) ?> tk</span></div>
      <div style="display:flex;justify-content:space-between;border-top:1px solid #EADFE2;padding-top:8px;margin-top:4px">
        <strong>Total</strong>
        <strong style="color:#561530;font-size:16px"><?= number_format((int) $order['total']) ?> tk</strong></div>
    </div>
  </section>

  <section style="display:flex;flex-direction:column;gap:16px">
    <div class="card">
      <div class="hd"><h2>Status</h2></div>
      <form method="post" action="<?= base_url('admin/orders/' . $order['id'] . '/status') ?>" style="padding:16px">
        <?= csrf_field() ?>
        <div class="field">
          <label for="status">Fulfilment status</label>
          <select id="status" name="status">
            <?php foreach ($statuses as $s): ?>
              <option value="<?= esc($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= esc($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <button class="btn" type="submit">Update status</button>
      </form>
    </div>

    <div class="card">
      <div class="hd"><h2>Details</h2></div>
      <div style="padding:16px;display:flex;flex-direction:column;gap:9px;font-size:13px">
        <?php foreach ($details as $k => $vv): ?>
          <div style="display:flex;gap:12px">
            <span class="muted" style="min-width:88px;font-weight:700;font-size:11.5px;letter-spacing:.06em;text-transform:uppercase"><?= esc($k) ?></span>
            <span style="flex:1"><?= esc($vv) ?></span>
          </div>
        <?php endforeach; ?>

        <?php if (!empty($order['map_url'])): ?>
          <div style="display:flex;gap:12px">
            <span class="muted" style="min-width:88px;font-weight:700;font-size:11.5px;letter-spacing:.06em;text-transform:uppercase">Map</span>
            <span style="flex:1">
              <a href="<?= esc($order['map_url'], 'attr') ?>" target="_blank" rel="noopener noreferrer">Open pinned location ↗</a>
            </span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <a class="btn ghost" href="<?= base_url('admin/orders') ?>">← Back to orders</a>
  </section>

</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
