<?php
$isEdit = $row !== null;
$v      = static fn (string $k, $d = '') => esc(old($k, $row[$k] ?? $d) ?? '', 'attr');
$action = $isEdit ? base_url('admin/gallery/' . $row['id']) : base_url('admin/gallery');
ob_start();
?>
<form method="post" action="<?= $action ?>" class="card" style="padding:18px;max-width:620px">
  <?= csrf_field() ?>

  <div class="field"><label for="src">Image URL</label>
    <input type="text" id="src" name="src" required value="<?= $v('src') ?>"></div>

  <div class="field"><label for="alt">Alt text <span class="muted">(describes the image for screen readers)</span></label>
    <input type="text" id="alt" name="alt" value="<?= $v('alt') ?>"></div>

  <div class="row">
    <div class="field"><label for="span">Grid span (1–3)</label>
      <input type="number" id="span" name="span" min="1" max="3" value="<?= $v('span', 1) ?>"></div>
    <div class="field"><label for="sort_order">Sort order</label>
      <input type="number" id="sort_order" name="sort_order" value="<?= $v('sort_order', 0) ?>"></div>
  </div>

  <div class="actions">
    <button class="btn" type="submit"><?= $isEdit ? 'Save changes' : 'Add image' ?></button>
    <a class="btn ghost" href="<?= base_url('admin/gallery') ?>">Cancel</a>
  </div>
</form>
<?php
$content = ob_get_clean();
include __DIR__ . '/../_layout.php';
