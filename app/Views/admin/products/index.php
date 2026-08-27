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
    <thead><tr><th>Photo</th><th>Code</th><th>Name</th><th>Category</th><th>Price</th><th>Min</th><th>Flags</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $p):
      $ph = $primary[(int) $p['id']] ?? null;
    ?>
      <tr>
        <td style="width:56px">
          <?php if ($ph): $isAi = $ph['is_ai']; ?>
            <span style="position:relative;display:block;width:44px;height:44px">
              <img src="<?= esc(base_url('uploads/products/' . $ph['path'])) ?>" alt="" loading="lazy"
                   style="width:44px;height:44px;border-radius:10px;object-fit:cover;display:block">
              <span title="<?= $isAi ? 'AI-generated image' : 'Real photograph' ?>"
                    style="position:absolute;right:-4px;bottom:-4px;width:17px;height:17px;border-radius:999px;
                           border:2px solid #fff;display:flex;align-items:center;justify-content:center;
                           font-size:9px;font-weight:700;color:#fff;line-height:1;
                           background:<?= $isAi ? '#9E1C60' : '#17693F' ?>"><?= $isAi ? '✦' : '◉' ?></span>
            </span>
          <?php else: ?>
            <span class="muted" style="display:flex;align-items:center;justify-content:center;width:44px;height:44px;
                       border:1px dashed #EADFE2;border-radius:10px;font-size:16px">·</span>
          <?php endif; ?>
        </td>
        <td><code style="font-size:12px;letter-spacing:.06em;color:#561530"><?= esc($p['code'] ?? '—') ?></code></td>
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
