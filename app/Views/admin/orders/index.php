<?php
$sc = static fn (string $s): array => match ($s) {
    'Delivered'        => ['#E8F5EE', '#17693F'],
    'Cancelled'        => ['#FDECEA', '#B3261E'],
    'Out for Delivery' => ['#FFF1D6', '#8A5A08'],
    'Preparing'        => ['#FDF3DF', '#8A5A08'],
    default            => ['#F1ECEE', '#561530'],
};
ob_start();
?>
<div class="card">
  <div class="hd">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Order no or customer…" style="width:210px">
      <select name="status" style="width:auto">
        <option value="">All statuses</option>
        <?php foreach ($statuses as $s): ?>
          <option value="<?= esc($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= esc($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn ghost sm" type="submit">Filter</button>
      <?php if ($q !== '' || $status !== ''): ?>
        <a class="btn ghost sm" href="<?= base_url('admin/orders') ?>">Clear</a>
      <?php endif; ?>
    </form>
    <span class="muted"><?= count($rows) ?> order(s)</span>
  </div>

  <?php if (!$rows): ?>
    <div class="empty">No orders match.</div>
  <?php else: ?>
    <div class="tablewrap"><table>
      <thead><tr><th>Order</th><th>Customer</th><th>Placed</th><th>Payment</th><th>Total</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $o): [$bg, $fg] = $sc($o['status']); ?>
        <tr>
          <td><a href="<?= base_url('admin/orders/' . $o['id']) ?>"><strong><?= esc($o['order_no']) ?></strong></a></td>
          <td><?= esc($o['customer_name'] ?: '—') ?>
            <div class="muted" style="font-size:12px"><?= esc($o['customer_email'] ?? '') ?></div></td>
          <td><?= esc($o['placed_on'] ?? '—') ?></td>
          <td><?= esc($o['payment_method'] ?? '—') ?></td>
          <td><strong><?= number_format((int) $o['total']) ?> tk</strong></td>
          <td><span class="pill" style="background:<?= $bg ?>;color:<?= $fg ?>"><?= esc($o['status']) ?></span></td>
          <td><a class="btn ghost sm" href="<?= base_url('admin/orders/' . $o['id']) ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
