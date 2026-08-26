<?php ob_start(); ?>
<div class="card">
  <div class="hd"><h2><?= count($rows) ?> testimonials</h2>
    <a class="btn" href="<?= base_url('admin/testimonials/create') ?>">+ New testimonial</a></div>

  <?php if (!$rows): ?>
    <div class="empty">No testimonials yet.</div>
  <?php else: ?>
    <div class="tablewrap"><table>
      <thead><tr><th>Name</th><th>Rating</th><th>Quote</th><th>Item</th><th>Live</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $t): ?>
        <tr>
          <td><strong><?= esc($t['name']) ?></strong></td>
          <td style="color:#F5AD18;white-space:nowrap"><?= str_repeat('★', (int) $t['stars']) ?></td>
          <td class="muted" style="max-width:400px"><?= esc(mb_strimwidth($t['quote'], 0, 120, '…')) ?></td>
          <td><?= esc($t['item'] ?? '—') ?></td>
          <td><?= $t['is_published']
                ? '<span class="pill" style="background:#E8F5EE;color:#17693F">Published</span>'
                : '<span class="pill" style="background:#F1ECEE;color:#75666B">Hidden</span>' ?></td>
          <td class="actions">
            <a class="btn ghost sm" href="<?= base_url('admin/testimonials/' . $t['id'] . '/edit') ?>">Edit</a>
            <form method="post" action="<?= base_url('admin/testimonials/' . $t['id'] . '/delete') ?>"
                  onsubmit="return confirm('Delete this testimonial?')">
              <?= csrf_field() ?><button class="btn danger sm" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
