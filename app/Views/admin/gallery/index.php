<?php ob_start(); ?>
<div class="card">
  <div class="hd"><h2><?= count($rows) ?> gallery images</h2>
    <a class="btn" href="<?= base_url('admin/gallery/create') ?>">+ Add image</a></div>

  <?php if (!$rows): ?>
    <div class="empty">No images yet.</div>
  <?php else: ?>
    <div style="padding:16px;display:grid;gap:14px;grid-template-columns:repeat(auto-fill,minmax(200px,1fr))">
      <?php foreach ($rows as $g): ?>
        <div style="border:1px solid #EADFE2;border-radius:12px;overflow:hidden;background:#fff">
          <div style="aspect-ratio:4/3;background:#F3E7D6">
            <img src="<?= esc($g['src']) ?>" alt="<?= esc($g['alt'] ?? '') ?>" loading="lazy"
                 style="width:100%;height:100%;object-fit:cover;display:block">
          </div>
          <div style="padding:11px">
            <div style="font-size:13px;font-weight:600"><?= esc($g['alt'] ?: 'Untitled') ?></div>
            <div class="muted" style="font-size:12px;margin-bottom:9px">span <?= (int) $g['span'] ?> · order <?= (int) $g['sort_order'] ?></div>
            <div class="actions">
              <a class="btn ghost sm" href="<?= base_url('admin/gallery/' . $g['id'] . '/edit') ?>">Edit</a>
              <form method="post" action="<?= base_url('admin/gallery/' . $g['id'] . '/delete') ?>"
                    onsubmit="return confirm('Delete this image?')">
                <?= csrf_field() ?><button class="btn danger sm" type="submit">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
