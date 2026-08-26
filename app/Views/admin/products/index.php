<?php ob_start(); ?>
<div class="card">
  <div class="hd">
    <form method="get" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Search products…" style="width:200px">
      <select name="category" style="width:auto">
        <option value="">All categories</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= $cat === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn ghost sm" type="submit">Filter</button>
      <?php if ($q !== '' || $cat > 0): ?><a class="btn ghost sm" href="<?= base_url('admin/products') ?>">Clear</a><?php endif; ?>
    </form>
    <a class="btn" href="<?= base_url('admin/products/create') ?>">+ New product</a>
  </div>

  <?php if (!$rows): ?>
    <div class="empty">No products match.</div>
  <?php else: ?>
  <div class="tablewrap"><table>
    <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Min</th><th>Flags</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $p): ?>
      <tr>
        <td>
          <strong><?= esc($p['name']) ?></strong>
          <div class="muted" style="font-size:12px"><?= esc($p['slug']) ?><?= $p['note'] ? ' · ' . esc($p['note']) : '' ?></div>
        </td>
        <td><?= esc($p['category_name'] ?? '—') ?></td>
        <td><strong><?= number_format((int) $p['price']) ?> tk</strong></td>
        <td><?= (int) $p['min_qty'] ?><?= $p['in_bagel_pool'] ? ' <span class="muted">(pool)</span>' : '' ?></td>
        <td style="white-space:nowrap">
          <?php if ($p['is_new']): ?><span class="pill" style="background:#F1E3EA;color:#9E1C60">New</span> <?php endif; ?>
          <?php if ($p['is_featured']): ?><span class="pill" style="background:#FDF3DF;color:#8A5A08">Featured</span> <?php endif; ?>
          <?php if (!$p['is_active']): ?><span class="pill" style="background:#F1ECEE;color:#75666B">Hidden</span><?php endif; ?>
        </td>
        <td class="actions">
          <a class="btn ghost sm" href="<?= base_url('admin/products/' . $p['id'] . '/edit') ?>">Edit</a>
          <form method="post" action="<?= base_url('admin/products/' . $p['id'] . '/delete') ?>"
                onsubmit="return confirm('Delete &quot;<?= esc($p['name'], 'attr') ?>&quot;? This cannot be undone.')">
            <?= csrf_field() ?><button class="btn danger sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <div style="padding:11px 15px" class="muted"><?= count($rows) ?> product(s)</div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../_layout.php';
