<?php ob_start(); ?>
<div class="card">
  <div class="hd">
    <h2><?= count($rows) ?> staff user(s)</h2>
    <a class="btn" href="<?= base_url('admin/users/create') ?>">+ New staff user</a>
  </div>

  <div class="tablewrap"><table>
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $u):
      $isMe   = (int) $u['id'] === $meId;
      $isLast = $u['role'] === 'admin' && $u['is_active'] && $adminCount <= 1;
    ?>
      <tr>
        <td>
          <strong><?= esc($u['name']) ?></strong>
          <?php if ($isMe): ?><span class="pill" style="background:#FDF3DF;color:#8A5A08;margin-left:6px">you</span><?php endif; ?>
        </td>
        <td><?= esc($u['email']) ?></td>
        <td>
          <?php if ($u['role'] === 'admin'): ?>
            <span class="pill" style="background:#F1E3EA;color:#9E1C60">admin</span>
          <?php else: ?>
            <span class="pill" style="background:#F1ECEE;color:#561530">staff</span>
          <?php endif; ?>
        </td>
        <td>
          <?= $u['is_active']
              ? '<span class="pill" style="background:#E8F5EE;color:#17693F">Active</span>'
              : '<span class="pill" style="background:#FDECEA;color:#B3261E">Disabled</span>' ?>
        </td>
        <td class="muted"><?= esc($u['last_login_at'] ?? '—') ?></td>
        <td class="actions">
          <a class="btn ghost sm" href="<?= base_url('admin/users/' . $u['id'] . '/edit') ?>">Edit</a>
          <?php if ($isMe): ?>
            <span class="muted" style="font-size:12px;align-self:center">can't delete yourself</span>
          <?php elseif ($isLast): ?>
            <span class="muted" style="font-size:12px;align-self:center">only admin</span>
          <?php else: ?>
            <form method="post" action="<?= base_url('admin/users/' . $u['id'] . '/delete') ?>"
                  onsubmit="return confirm('Delete &quot;<?= esc($u['name'], 'attr') ?>&quot;? They lose dashboard access immediately.')">
              <?= csrf_field() ?><button class="btn danger sm" type="submit">Delete</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>

  <div style="padding:12px 16px;font-size:12.5px" class="muted">
    Administrators can manage staff users, products and orders, and delete records.
    Staff can edit content but cannot delete or reach this page.
  </div>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
