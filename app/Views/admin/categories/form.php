<?php
$isEdit = $row !== null;
$v = static fn(string $k, $d = '') => esc(old($k, $row[$k] ?? $d) ?? '', 'attr');
$action = $isEdit ? base_url('admin/categories/' . $row['id']) : base_url('admin/categories');
ob_start(); ?>
<form method="post" action="<?= $action ?>" class="card" style="padding:18px;max-width:680px">
  <?= csrf_field() ?>
  <div class="row">
    <div class="field"><label for="name">Name</label>
      <input type="text" id="name" name="name" required value="<?= $v('name') ?>"></div>
    <div class="field"><label for="slug">Slug <span class="muted">(blank = auto)</span></label>
      <input type="text" id="slug" name="slug" value="<?= $v('slug') ?>"></div>
  </div>
  <div class="field"><label for="blurb">Blurb</label>
    <textarea id="blurb" name="blurb" rows="2"><?= esc(old('blurb', $row['blurb'] ?? '') ?? '') ?></textarea></div>
  <div class="row">
    <div class="field"><label for="image">Image URL</label>
      <input type="text" id="image" name="image" value="<?= $v('image') ?>"></div>
    <div class="field"><label for="sort_order">Sort order</label>
      <input type="number" id="sort_order" name="sort_order" value="<?= $v('sort_order', 0) ?>"></div>
  </div>
  <div class="actions"><button class="btn" type="submit"><?= $isEdit ? 'Save changes' : 'Create category' ?></button>
    <a class="btn ghost" href="<?= base_url('admin/categories') ?>">Cancel</a></div>
</form>
<?php $content = ob_get_clean(); include __DIR__ . '/../_layout.php';
