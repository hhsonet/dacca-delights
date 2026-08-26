<?php
$statusColour = static function (string $s): array {
    return match ($s) {
        'Delivered'        => ['#E8F5EE', '#17693F'],
        'Cancelled'        => ['#FDECEA', '#B3261E'],
        'Out for Delivery' => ['#FFF1D6', '#8A5A08'],
        'Preparing'        => ['#FDF3DF', '#8A5A08'],
        default            => ['#F1ECEE', '#561530'],
    };
};

ob_start();
?>
<div class="grid" style="margin-bottom:18px">
  <div class="stat"><div class="k">TOTAL ORDERS</div><div class="v"><?= (int) $stats['orders'] ?></div></div>
  <div class="stat"><div class="k">REVENUE</div><div class="v"><?= number_format($stats['revenue']) ?> tk</div>
    <div class="muted" style="font-size:12px;margin-top:3px">excludes cancelled</div></div>
  <div class="stat"><div class="k">PRODUCTS</div><div class="v"><?= (int) $counts['products'] ?></div></div>
  <div class="stat"><div class="k">CUSTOMERS</div><div class="v"><?= (int) $counts['customers'] ?></div></div>
</div>

<div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">

  <section class="card">
    <div class="hd"><h2>Recent orders</h2><a class="btn ghost sm" href="<?= base_url('admin/orders') ?>">All orders</a></div>
    <?php if (!$recent): ?>
      <div class="empty">No orders yet.</div>
    <?php else: ?>
      <div class="tablewrap"><table>
        <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $o): [$bg, $fg] = $statusColour($o['status']); ?>
          <tr>
            <td><a href="<?= base_url('admin/orders/' . $o['id']) ?>"><strong><?= esc($o['order_no']) ?></strong></a>
                <div class="muted" style="font-size:12px"><?= esc($o['placed_on'] ?? '') ?></div></td>
            <td><?= esc($o['customer_name'] ?: '—') ?></td>
            <td><strong><?= number_format((int) $o['total']) ?> tk</strong></td>
            <td><span class="pill" style="background:<?= $bg ?>;color:<?= $fg ?>"><?= esc($o['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>
  </section>

  <section class="card">
    <div class="hd"><h2>Best sellers</h2><span class="muted" style="font-size:12px">by units sold</span></div>
    <?php if (!$topSellers): ?>
      <div class="empty">No sales recorded yet.</div>
    <?php else: ?>
      <?php $max = max($topSellers); ?>
      <div style="padding:14px 16px;display:flex;flex-direction:column;gap:11px">
        <?php foreach ($topSellers as $name => $units): ?>
          <div>
            <div style="display:flex;justify-content:space-between;gap:10px;font-size:13px;margin-bottom:4px">
              <span><?= esc($name) ?></span><strong><?= (int) $units ?></strong>
            </div>
            <div style="height:7px;border-radius:99px;background:#F1ECEE;overflow:hidden">
              <div style="height:100%;width:<?= $max > 0 ? round($units / $max * 100) : 0 ?>%;background:#F5AD18"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/_layout.php';
