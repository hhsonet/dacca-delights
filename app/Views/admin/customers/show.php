<?php
$profile = [
    'Name'           => trim($row['first_name'] . ' ' . ($row['last_name'] ?? '')),
    'Email'          => $row['email'] ?? '—',
    'Phone'          => $row['phone'] ?? '—',
    'WhatsApp'       => $row['whatsapp'] ?? '—',
    'Account'        => $row['password_hash'] ? 'Registered' : 'Guest (no login)',
    'Email verified' => $row['email_verified'] ? 'Yes' : 'No',
    'Last login'     => $row['last_login_at'] ?? '—',
    'Joined'         => $row['created_at'] ?? '—',
];
ob_start();
?>
<div style="display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));align-items:start">

  <div class="card">
    <div class="hd"><h2>Profile</h2></div>
    <div style="padding:16px;display:flex;flex-direction:column;gap:9px;font-size:13px">
      <?php foreach ($profile as $k => $vv): ?>
        <div style="display:flex;gap:12px">
          <span class="muted" style="min-width:104px;font-weight:700;font-size:11.5px;letter-spacing:.06em;text-transform:uppercase"><?= esc($k) ?></span>
          <span style="flex:1"><?= esc($vv) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="hd"><h2>Orders (<?= count($orders) ?>)</h2></div>
    <?php if (!$orders): ?>
      <div class="empty">No orders yet.</div>
    <?php else: ?>
      <div class="tablewrap"><table>
        <thead><tr><th>Order</th><th>Placed</th><th>Total</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><a href="<?= base_url('admin/orders/' . $o['id']) ?>"><strong><?= esc($o['order_no']) ?></strong></a></td>
            <td><?= esc($o['placed_on'] ?? '—') ?></td>
            <td><?= number_format((int) $o['total']) ?> tk</td>
            <td><?= esc($o['status']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>
  </div>

</div>
<p style="margin-top:16px"><a class="btn ghost" href="<?= base_url('admin/customers') ?>">← Back to customers</a></p>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
