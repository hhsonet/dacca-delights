<?php ob_start(); ?>
<div class="card">
  <div class="hd">
    <form method="get" style="display:flex;gap:8px">
      <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search customers…" style="width:220px">
      <button class="btn ghost sm" type="submit">Search</button>
      <?php if ($q !== ''): ?>
        <a class="btn ghost sm" href="<?= base_url('admin/customers') ?>">Clear</a>
      <?php endif; ?>
    </form>
    <span class="muted"><?= count($rows) ?> customer(s)</span>
  </div>

  <?php if (!$rows): ?>
    <div class="empty">No customers match.</div>
  <?php else: ?>
    <div class="tablewrap"><table>
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Account</th><th>Last login</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $c): ?>
        <tr>
          <td><strong><?= esc(trim($c['first_name'] . ' ' . ($c['last_name'] ?? ''))) ?></strong></td>
          <td><?= esc($c['email'] ?? '—') ?></td>
          <td><?= esc($c['phone'] ?? '—') ?></td>
          <td>
            <?php if ($c['password_hash']): ?>
              <span class="pill" style="background:#E8F5EE;color:#17693F">Registered</span>
            <?php else: ?>
              <span class="pill" style="background:#F1ECEE;color:#75666B">Guest</span>
            <?php endif; ?>
          </td>
          <td class="muted"><?= esc($c['last_login_at'] ?? '—') ?></td>
          <td><a class="btn ghost sm" href="<?= base_url('admin/customers/' . $c['id']) ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
