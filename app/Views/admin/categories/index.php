<?php ob_start(); ?>
<div class="card">
  <div class="hd"><h2><?= count($rows) ?> categories</h2>
    <a class="btn" href="<?= base_url('admin/categories/create') ?>">+ New category</a></div>
  <?php if (!$rows): ?><div class="empty">No categories yet.</div><?php else: ?>
  <div class="tablewrap"><table>
    <thead><tr><th>Name</th><th>Blurb</th><th>Products</th><th>Order</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $c): ?>
      <tr>
        <td><strong><?= esc($c['name']) ?></strong><div class="muted" style="font-size:12px"><?= esc($c['slug']) ?></div></td>
        <td class="muted" style="max-width:380px"><?= esc($c['blurb'] ?? '—') ?></td>
        <td><?= (int) ($counts[$c['id']] ?? 0) ?></td>
        <td><?= (int) $c['sort_order'] ?></td>
        <td class="actions">
          <a class="btn ghost sm" href="<?= base_url('admin/categories/' . $c['id'] . '/edit') ?>">Edit</a>
          <form method="post" action="<?= base_url('admin/categories/' . $c['id'] . '/delete') ?>"
                onsubmit="return confirm('Delete this category?')">
            <?= csrf_field() ?><button class="btn danger sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../_layout.php';
